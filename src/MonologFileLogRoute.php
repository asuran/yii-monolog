<?php

namespace YiiMonolog;

use Monolog\Logger;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Monolog\Registry;

class MonologFileLogRoute extends \CLogRoute
{
    /** @var LoggerInterface */
    protected $logger;

    /** @var string */
    public $name = 'application';

    /** @var string */
    public $formatter;

    /** @var string */
    public $stream;

    /** @var array */
    public $processors = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->logger = $this->getMonolog();
    }

    /**
     * @return LoggerInterface
     */
    protected function getMonolog()
    {
        $key = md5(serialize([
            $this->name,
            $this->formatter,
            $this->stream,
            $this->processors
        ]));

        if (Registry::hasLogger($key)) {
            return Registry::getInstance($key);
        }

        $logger = new Logger($this->name);

        $logger->pushHandler($this->createHandler([
            'class' => 'Monolog\Handler\StreamHandler',
            'stream' => $this->stream,
            'formatter' => $this->formatter,
            'bubble' => true,
            'filePermission' => 0666,
        ]));

        foreach ($this->processors as $processor) {
            $logger->pushProcessor($this->createProcessor($processor));
        }

        Registry::addLogger($logger, $key);

        return $logger;
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

        throw new InvalidArgumentException("Level $level not allowed for logs");
    }
}
