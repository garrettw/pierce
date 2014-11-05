<?php
namespace Pierce;
use Noair\Noair,
    Noair\Listener,
    Noair\Event;

class StdEvents extends Listener
{
    public function __construct(Client $client)
    {
        $this->defaultPriority = Noair::PRIORITY_HIGHEST;
        $this->handlers = [
            ['timer:' . ($client->rxtimeout * 125), [$this, 'pingCheck']],
        ];
    }
    public function onReceived(Event $e)
    {
        $msg = $e->data;
    }

    public function pingCheck(Event $e)
    {
        $time = time();
        $caller = $e->caller;

        if ($time - $caller->_lastrx > $caller->_rxtimeout):
            $caller->reconnect();
            $caller->_lastrx = $time;
        elseif ($time - $caller->_lastrx > $caller->_rxtimeout/2):
            $this->noair->publish(new Event('send', [
                'connection' => $caller->name,
                'message' => 'PING '.$caller->address,
                'priority' => SMARTIRC_CRITICAL,
            ], $this));
        endif;
    }
}
