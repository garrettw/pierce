<?php

namespace Pierce\Numerics;

class IRCnet extends RFC
{
    // New numerics
    const RPL_YOURID = '042';
    const RPL_SAVENICK = '043';
    const RPL_STATSIAUTH = '239';
    const RPL_STATSSLINE = '245';
    const RPL_STATSDEFINE = '248';
    const RPL_STATSDELTA = '274';
    const ERR_TOOMANYMATCHES = '416';
    const ERR_DEAD = '438';
    const ERR_CHANTOORECENT = '487';
    const ERR_TSLESSCHAN = '488';
}
