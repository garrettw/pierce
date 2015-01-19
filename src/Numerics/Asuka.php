<?php

namespace Pierce\Numerics;

class Asuka extends Ircu
{ // used by QuakeNet
    // New numerics
    const RPL_CHKHEAD = '286';
    const RPL_CHANUSER = '287';
    const RPL_PATCHHEAD = '288';
    const RPL_PATCHCON = '289';
    const RPL_DATASTR = '290';
    const RPL_ENDOFCHECK = '291';
    const RPL_NAMREPLY_ = '355';
    const ERR_BADHOSTMASK = '550';
    const ERR_HOSTUNAVAIL = '551';
    const ERR_USINGSLINE = '552';
    const ERR_STATSSLINE = '553';

    // Redefined numerics
    const RPL_GLIST_HASH = '285rfc';
    const RPL_NEWHOSTIS = '285';
    const ERR_UNIQOPRIVSNEEDED = '485rfc';
    const ERR_ISREALSERVICE = '485';
    const ERR_NONONREG = '486rfc';
    const ERR_ACCOUNTONLY = '486';
}
