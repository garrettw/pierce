<?php

namespace Pierce\Numerics;

class Hybrid extends RFC
{
    // New numerics
    const RPL_YOURCOOKIE = '014';
    const RPL_STATSPLINE = '220';
    const RPL_STATSFLINE = '224';
    const RPL_STATSDLINE = '225';
    const RPL_STATSULINE = '246';
    const RPL_STATSDEBUG = '249';
    const RPL_NOTOPERANYMORE = '385';
    const ERR_BADCHANNAME = '479';
    const ERR_GHOSTEDCLIENT = '503';

    // Redefined numerics
    const RPL_STATSBLINE = '247rfc';
    const RPL_STATSXLINE = '247';
    const ERR_RESTRICTED = '484rfc';
    const ERR_DESYNC = '484';
}
