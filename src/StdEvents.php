<?php
namespace Pierce;
use Noair\Event,
    Pierce\Connection\Message;

class StdEvents extends \Noair\Listener
{
    public function __construct(Client $client)
    {
        $this->defaultPriority = \Noair\Noair::PRIORITY_HIGHEST;
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
        if (is_numeric($msg->cmd)):
            $cmdTranslated = strtolower($e->caller->type->code[$msg->cmd]);
            $cmdTranslated = substr($cmdTranslated, 0, 3)
                            .ucfirst(substr($cmdTranslated, 4));
            // so now '001' is 'rplWelcome'
            $this->noair->publish(new Event($cmdTranslated, $msg, $e->caller));
        else:
            $this->noair->publish(new Event(strtolower($msg->cmd), $msg, $e->caller));
        endif;
    }

    public function onRplWelcome(Event $e)
    {
        // TODO: Figure out how to do this without telling the server our nick again!
        // $e->caller->nick = $e->data->params[0];
    }

    public function onRplMotdstart(Event $e)
    {
        $e->caller->motd[] = $e->data->body;
    }

    public function onRplMotd(Event $e)
    {
        $e->caller->motd[] = $e->data->body;
    }

    public function onRplEndofmotd(Event $e)
    {
        $e->caller->motd[] = $e->data->body;
    }

    public function onRplUmodeis(Event $e)
    {
        $e->caller->usermode = $e->data->body;
    }

    public function onRplChannelmodeis(Event $e)
    {
        // TODO: implement
    }

    public function onRplWhoreply(Event $e)
    {
        // TODO: implement
    }

    public function onRplNamreply(Event $e)
    {
        // TODO: implement
    }

    public function onRplBanlist(Event $e)
    {
        // TODO: implement
    }

    public function onRplTopic(Event $e)
    {
        // TODO: implement
    }

    public function onErrNicknameinuse(Event $e)
    {
        $newnick = substr($e->caller->nick, 0, 5) . rand(0, 999);
        // TODO: send $newnick to server
    }
}
