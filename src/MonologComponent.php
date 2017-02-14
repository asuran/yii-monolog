<?php

namespace YiiMonolog;

use Monolog\Logger;
use Monolog\Registry;
use Monolog\Handler\HandlerInterface;
use Monolog\Formatter\FormatterInterface;

class MonologComponent extends \CApplicationComponent
{
    /** @var string */
    public $name = 'application';
    /** @var string */
    public $loggerName = 'main';
    /** @var array */
    public $handlers = [];
    /** @var array */
    public $processors = [];

    /**
     * @inheritdoc
     * @throws RuntimeException
     */
    public function init()
    {
        $logger = new Logger($this->name);

        foreach ($this->handlers as $handler) {
            $logger->pushHandler($this->createHandler($handler));
        }

        foreach ($this->processors as $processor) {
            $logger->pushProcessor($this->createProcessor($processor));
        }

        Registry::addLogger($logger, $this->loggerName);

        parent::init();
    }

    /**
     * @param string|array $config
     *
     * @throws RuntimeException
     * @return HandlerInterface
     */
    protected function createHandler($config)
    {
        if (isset($config['formatter'])) {
            $formatterConfig = $config['formatter'];
            unset($config['formatter']);
        }

        /** @var HandlerInterface $instance */
        if (is_array($config)) {
            $instance = call_user_func_array(['Yii', 'createComponent'], $config);
        } else {
            $instance = \Yii::createComponent($config);
        }

        if (isset($formatterConfig)) {
            $formatter = $this->createFormatter($formatterConfig);
            $instance->setFormatter($formatter);
        }

        return $instance;
    }

    /**
     * @param array|string $config
     *
     * @throws RuntimeException
     * @return Closure
     */
    protected function createProcessor($config)
    {
        try {
            if (is_array($config)) {
                $instance = call_user_func_array(['Yii', 'createComponent'], $config);
            } else {
                $instance = \Yii::createComponent($config);
            }
            if (is_callable($instance)) {
                return $instance;
            }
        } catch(Exception $exception) {}

        throw new RuntimeException(
            'Unknown processor type, must be a Closure or a valid config for an invokable component'
        );
    }

    /**
     * @param string|array $config
     *
     * @return FormatterInterface
     */
    protected function createFormatter($config)
    {
        if (is_array($config)) {
            $instance = call_user_func_array(['Yii', 'createComponent'], $config);
        } else {
            $instance = \Yii::createComponent($config);
        }

        return $instance;
    }
}
