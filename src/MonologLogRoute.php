<?php

namespace YiiMonolog;

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
            $this->logger->log(
                $this->levelToString($log[1]),
                $log[0],
                [
                    'category' => $log[2],
                    'timestamp' => $log[3],
                ]
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
        switch ($level) {
            default:
            case \CLogger::LEVEL_PROFILE:
            case \CLogger::LEVEL_TRACE:
                return 'DEBUG';
            case \CLogger::LEVEL_WARNING:
                return 'WARNING';
            case \CLogger::LEVEL_ERROR:
                return 'ERROR';
            case \CLogger::LEVEL_INFO:
                return 'INFO';
        }
    }
}
