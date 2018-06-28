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
            if ($level === 'DEBUG' && !empty(getenv('APP_DEBUG')) && getenv('APP_DEBUG') == 'false') {
                continue;
            }
            $this->logger->log(
                $level,
                $log[0],
                is_array($log[2]) ? $log[2] : ['category' => $log[2]]
            );
        }
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
