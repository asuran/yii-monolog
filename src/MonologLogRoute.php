<?php

namespace YiiMonolog;

use Monolog\Logger;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Monolog\Registry;

class MonologLogRoute extends \CLogRoute
{
    /** @var string */
    public $loggerName = 'main';
    /** @var LoggerInterface */
    protected $logger;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->logger = Registry::getInstance($this->loggerName);
    }

    /**
     * @inheritdoc
     */
    protected function processLogs($logs)
    {
        foreach ($logs as $log) {
            $level = $this->levelToString($log[1]);
            if ($level === 'DEBUG' && ((defined('YII_DEBUG') && YII_DEBUG == false) || !defined('YII_DEBUG'))) {
                continue;
            }
            $this->logger->log(
                $level,
                $log[0],
                $this->resolveLogVariables($log[2])
            );
        }
    }

    /**
     * If receiving an array as 3th parameter in Yii::log(), log it as variables
     * If receiving a string (category_log as default in Yii), transform it in an array and log it
     * @param $data array|string
     * @return array
     */
    private function resolveLogVariables($data)
    {
        if (empty($data)) {
            return [];
        }
        if (is_array($data)) {
            return $data;
        }
        return [
            defined('VAR_LOG_CATEGORY_NAME') ? VAR_LOG_CATEGORY_NAME : 'categoria' => $data,
        ];
    }

    /**
     * Convert Yii level string to monolog format
     * @param string $level
     *
     * @return string
     */
    private function levelToString($level)
    {
        $allowed = ['DEBUG', 'INFO', 'NOTICE', 'WARNING', 'ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'];
        if (in_array(strtoupper($level), $allowed)) {
            return strtoupper($level);
        }

        if (in_array(strtoupper($level), ['PROFILE', 'TRACE'])) {
            return 'DEBUG';
        }

        if (is_int($level)) {
            return Logger::getLevelName($level);
        }

        throw new InvalidArgumentException("Level ${level} not allowed for logs");
    }
}
