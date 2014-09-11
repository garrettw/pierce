<?php

namespace Pierce\Exception;
use DavidRockin\Podiya\Podiya,
    DavidRockin\Podiya\Event;

class Exception extends \Exception
{
    public function __construct(Podiya $podiya, $message = null, $code = 0,
                                Exception $previous = null)
    {
        // mainly for the purpose of logging
        $podiya->publish(new Event('exception',
            [$code => $message, 'previous' => $previous], $this, $podiya));
        parent::__construct($message, $code, $previous);
    }
    
    public function __toString()
    {
        return __CLASS__ . " [{$this->code}]: {$this->message} ("
            . $this->getFile() . ':' . $this->getLine() . ')';
    }
}
