<?php

namespace Pierce;
use Noair\Noair,
    Noair\Listener,
    Noair\Event;

class Logger extends Listener
{
    private $logger;

    public function __construct(\Monolog\Logger $logger)
    {
        $this->handlers[] = ['all', [$this, 'log'], Noair::PRIORITY_URGENT, true];
        $this->logger = $logger;
    }

    public function log(Event $e)
    {

    }
}
