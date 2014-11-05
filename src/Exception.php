<?php

namespace Pierce;
use Noair\Noair,
    Noair\Event;

class Exception extends \Exception
{
    public function __construct(Noair $noair, $message = null, $code = 0,
                                Exception $previous = null)
    {
        // mainly for the purpose of logging
        $noair->publish(new Event('exception',
            [$code => $message, 'previous' => $previous], $this));
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return __CLASS__ . " [{$this->code}]: {$this->message} ("
            . $this->getFile() . ':' . $this->getLine() . ')';
    }
}
