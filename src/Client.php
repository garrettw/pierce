<?php

namespace Pierce;
use DavidRockin\Podiya\Podiya,
    DavidRockin\Podiya\Event,
    Monolog\Logger;

class Client
{
    private $eventmgr;
    private $logger;
    
    public function __construct(Podiya $eventmgr, Logger $logger)
    {
        $this->eventmgr = $eventmgr;
        $this->logger   = $logger;
    }
}
