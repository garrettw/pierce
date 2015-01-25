<?php
namespace Pierce;
use Noair\Event,
    Pierce\Connection\Message,
    Pierce\Event\RawSendEvent,
    Pierce\Event\SendEvent;

class StdEvents extends \Noair\Listener
{
    private $rxtimeout;

    public function __construct(Client $client)
    {
        $this->defaultPriority = \Noair\Noair::PRIORITY_HIGHEST;
        $this->rxtimeout = $client->rxtimeout;
        $this->handlers = [
            ['timer:' . ($this->rxtimeout / 8), [$this, 'pingCheck']],
        ];
    }

    public function pingCheck(Event $e)
    {
        if (!empty($e->data)):
            return;
        endif;

        $conn = $e->caller;
        $time = time();

        if ($conn->lastrx + $this->rxtimeout < $time):
            $this->noair->publish(new Event('rxTimeout', $conn->name, $this));
        elseif ($conn->lastrx + $this->rxtimeout/2 < $time):
            $this->noair->publish(new RawSendEvent([
                'connection' => $conn->name,
                'message' => 'PING ' . $conn->address,
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
        $cmdName = (is_numeric($msg->cmd))
            ? strtolower($e->caller->type->code[$msg->cmd])
            : strtolower($msg->cmd);

        if (strpos($cmdName, '_')) {
            $parts = explode('_', $cmdName);
            $partcount = count($parts);

            for ($i = 1; $i < $partcount; $i++) {
                $parts[$i] = ucfirst($parts[$i]);
            }

            $cmdName = implode($parts);
        }

        $this->noair->publish(new Event($cmdName, $msg, $e->caller));
    }

    public function onChanmsg(Event $e)
    {

    }

    public function onChanmsgAction(Event $e)
    {

    }

    public function onChannotice(Event $e)
    {

    }

    public function onCtcp(Event $e)
    {

    }

    public function onCtcpReply(Event $e)
    {

    }

    public function onErrNicknameinuse(Event $e)
    {
        $newnick = substr($e->caller->nick, 0, 5) . rand(0, 999);
        // TODO: send $newnick to server
    }

    public function onJoin(Event $e)
    {
        // did we just join a channel, or did someone else join one we're in?
        // either create channel and get its info, or retrieve chan. Add user.
    }

    public function onKick(Event $e)
    {
        // is it us? remove user from channel
    }

    public function onMode(Event $e)
    {
        // is it us? update user
    }

    public function onNick(Event $e)
    {
        // is it us? find user and update him
    }

    public function onNotice(Event $e)
    {

    }

    public function onQuit(Event $e)
    {
        // remove user from channel
    }

    public function onPart(Event $e)
    {
        // is it us? remove user from channel
    }

    public function onPing(Event $e)
    {
        $this->noair->publish(new RawSendEvent([
            'connection' => $e->caller->name,
            'message' => "PONG :" . $e->data->body,
            'priority' => Message::URGENT,
        ], $this));
    }

    public function onPrivmsg(Event $e)
    {

    }

    public function onPrivmsgAction(Event $e)
    {

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

    public function onSendQuit(SendEvent $e)
    {
        $message = isset($e->data['message'])
            ? " :" . $e->data['message']
            : "";

        $this->noair->publish(new RawSendEvent([
            'connection' => $e->data['connection'],
            'message' => "QUIT" . $message,
            'priority' => $e->data['priority'],
        ], $this));

        $this->noair->publish(new Event('disconnect', $e->data['connection'], $this));
    }

    public function onTopic(Event $e)
    {
        // update topic
    }
}
