<?php

namespace Pierce\Event;
use Pierce\Connection,
    Pierce\Exception as PException;

class RawSendEvent extends \Noair\Event
{
    public function __construct($data, $caller)
    {
        if (is_array($data)
            && isset($data['message']) && is_string($data['message'])
            && isset($data['priority']) && is_int($data['priority'])
            && ((isset($data['connection']) && is_string($data['connection']))
                || ($caller instanceof Connection && $data['connection'] = $caller->name)
            )
        ):
            parent::__construct('rawSend', $data, $caller);
        else:
            throw new PException($caller->noair, 'Invalid RawSendEvent data');
        endif;
    }
}
