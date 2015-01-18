<?php

namespace Pierce\Numerics;

class Undernet extends RFC
{
    // New numerics
    const RPL_HOSTHIDDEN = '396';
    const ERR_NOTIMPLEMENTED = '449';
    const ERR_VOICENEEDED = '489';

    // Redefined numerics
    const ERR_RESTRICTED = '484rfc';
    const ERR_ISCHANSERVICE = '484';
}
