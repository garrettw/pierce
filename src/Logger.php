<?php

namespace Pierce;
use Noair\Event;

class Logger extends \Noair\Listener
{
    private $logger;

    public function __construct(\Monolog\Logger $logger)
    {
        $this->handlers[] = ['all', [$this, 'log'], \Noair\Noair::PRIORITY_URGENT, true];
        $this->logger = $logger;
    }

    public function log(Event $e)
    {
        if ($e->name != 'timer'):
            echo $e->name . "\n";
            print_r($e->data);
            echo "\n";
        endif;
    }
}
