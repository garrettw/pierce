<?php

namespace Pierce\Connection;

class Message
{
    const URGENT = 0;
    const HIGH   = 1;
    const NORMAL = 2;
    const LOW    = 3;

    private $raw;
    private $src = '';
    private $srcNick = '';
    private $srcIdent = '';
    private $srcHost = '';
    private $cmd;
    private $params = [];
    private $body = '';

    public function __construct($rawmsg)
    {
        $this->raw = $rawmsg;

        // parse out the prefix
        $prefixEnd = -1;
        if ($rawmsg{0} == ':'):
            $prefixEnd = strpos($rawmsg, ' ');
            $this->src = substr($rawmsg, 1, $prefixEnd - 1);
            // parse ident thingy
            if (preg_match('/^(\S+)!(\S+)@(\S+)$/', $this->src, $matches)):
                $this->srcNick  = $matches[1];
                $this->srcIdent = $matches[2];
                $this->srcHost  = $matches[3];
            else:
                $this->srcHost = $this->src;
            endif;
        endif;

        // parse out the trailing
        if ($trailingStart = strpos($rawmsg, ' :')): // this is not ==
            $this->body = substr($rawmsg, $trailingStart + 2);
        else:
            $trailingStart = strlen($rawmsg);
        endif;

        // parse out command and params
        $this->params = explode(' ', substr($rawmsg, $prefixEnd + 1,
                                            $trailingStart - $prefixEnd - 1));
        $this->cmd = array_shift($this->params);

        if (!is_numeric($this->cmd)):
            if ($this->cmd == 'PRIVMSG'):
                if (strspn($this->params[0], '&#+!')):
                    $this->cmd = 'CHANMSG';
                endif;
                if ($this->body{0} == chr(1)):
                    if (preg_match("/^\1ACTION .*\1\$/", $this->body)):
                        $this->cmd .= '_ACTION';
                    elseif (preg_match("/^\1.*\1\$/", $this->body)):
                        $this->cmd = 'CTCP';
                    endif;
                endif;
            elseif ($this->cmd == 'NOTICE'):
                if (strspn($this->params[0], '&#+!')):
                    $this->cmd = 'CHANNOTICE';
                elseif (preg_match("/^\1.*\1\$/", $this->body)):
                    $this->cmd = 'CTCP_REPLY';
                endif;
            endif;
        endif;
    }

    public function __get($name)
    {
        return $this->$name;
    }
}
