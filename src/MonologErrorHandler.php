<?php

namespace YiiMonolog;

use Monolog\Registry;
use Monolog\ErrorHandler;

class MonologErrorHandler extends \CErrorHandler
{
    /** @var string */
    public $loggerName = 'main';
    /** @var Monolog\ErrorHandler */
    protected $errorHandler;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $logger = Registry::getInstance($this->loggerName);
        $this->errorHandler = new ErrorHandler($logger);
    }

    /**
     * @inheritdoc
     */
    protected function handleException($e)
    {
        $this->errorHandler->handleException($e);
        parent::handleException($e);
    }

    /**
     * @inheritDoc
     */
    protected function handleError($event)
    {
        $this->errorHandler->handleError($event->code, $event->message, $event->file, $event->line, $event->params);
        parent::handleError($event);
    }
}
