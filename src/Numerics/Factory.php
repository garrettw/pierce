<?php

namespace Pierce\Numerics;

class Factory implements \Pierce\FactoryInterface
{
    private $type;

    public function __construct($t = '')
    {
        if (class_exists("Pierce\Numerics\\$t")):
            $this->type = $t;
        elseif (class_exists('Pierce\Numerics\\' . ucfirst($t))):
            $this->type = ucfirst($t);
        else:
            $map = [
                'quakenet' => 'Asuka',
                'austnet' => 'AustHex',
                'dalnet' => 'Bahamut',
                'ircnet' => 'IRCnet',
                'freenode' => 'Ircu',
                'undernet' => 'Ircu',
                'ptlink' => 'PTlink',
                'rfc' => 'RFC',
            ];
            $lowername = strtolower($t);

            if (isset($map[$lowername])):
                $this->type = $map[$lowername];
            else:
                $this->type = 'RFC';
            endif;
        endif;
    }

    public function create()
    {
        $classname = '\Pierce\Numerics\\' . $this->type;
        return new $classname();
    }
}
