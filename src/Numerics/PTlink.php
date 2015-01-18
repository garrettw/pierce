<?php

namespace Pierce\Numerics;

class PTlink extends RFC
{
    // New numerics
    const RPL_MAPMORE = '615';

    // Redefined numerics
    const RPL_STATSBLINE = '247rfc';
    const RPL_STATSXLINE = '247';
    const ERR_RESTRICTED = '484rfc';
    const ERR_DESYNC = '484';
    const ERR_UNIQOPRIVSNEEDED = '485rfc';
    const ERR_CANTKICKADMIN = '485';
}
