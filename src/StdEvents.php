<?php
namespace Pierce;
use Noair\Noair,
    Noair\Listener,
    Noair\Event,
    Pierce\Connection\Message;

class StdEvents extends Listener
{
    public function __construct(Client $client)
    {
        $this->defaultPriority = Noair::PRIORITY_HIGHEST;
        $this->handlers = [
            ['timer:' . ($client->rxtimeout * 125), [$this, 'pingCheck']],
        ];
    }

    public function pingCheck(Event $e)
    {
        $caller = $e->caller;
        $time = Connection::currentTimeMillis();

        if ($caller->lastrx + $caller->rxtimeout < $time):
            $this->noair->publish(new Event('reconnect', $caller->name, $this));
        elseif ($caller->lastrx + $caller->rxtimeout/2 < $time):
            $this->noair->publish(new Event('send', [
                'connection' => $caller->name,
                'message' => 'PING ' . $caller->address,
                'priority' => Message::URGENT,
            ], $this));
        endif;
    }

    public function onConnectionPropertyChange(Event $e)
    {
        switch ($e->data[0]):
            case 'nick':

            case 'username':

            case 'realname':

        endswitch;
    }

    public function onReceived(Event $e)
    {
        $msg = $e->data;
        // analyze incoming message and re-publish a more specific event
    }
}
