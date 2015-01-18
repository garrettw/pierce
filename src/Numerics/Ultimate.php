<?php

namespace Pierce\Numerics;

class Ultimate extends RFC
{
    // New numerics
    const RPL_STATSDLINE = '275';
    const RPL_IRCOPS = '386';
    const RPL_ENDOFIRCOPS = '387';
    const ERR_NORULES = '434';
    const ERR_NCHANGETOOFAST = '438';
    const ERR_NEEDPONG = '513';
    const RPL_WATCHCLEAR = '608';
    const RPL_ISOPER = '610';
    const RPL_ISLOCOP = '611';
    const RPL_ISNOTOPER = '612';
    const RPL_ENDOFISOPER = '613';
    const RPL_WHOISMODES = '615';
    const RPL_WHOISHOST = '616';
    const RPL_WHOISBOT = '617';
    const RPL_WHOWASHOST = '619';
    const RPL_RULESSTART = '620';
    const RPL_RULES = '621';
    const RPL_ENDOFRULES = '622';
    const RPL_MAPMORE = '623';
    const RPL_OMOTDSTART = '624';
    const RPL_OMOTD = '625';
    const RPL_ENDOFO = '626';
    const RPL_SETTINGS = '630';
    const RPL_ENDOFSETTINGS = '631';

    // Redefined numerics
    const RPL_BOUNCE = '005rfc';
    const RPL_PROTOCTL = '005';
    const RPL_EXCEPTLIST = '348rfc';
    const RPL_EXLIST = '348';
    const RPL_ENDOFEXCEPTLIST = '349rfc';
    const RPL_ENDOFEXLIST = '349';
}
