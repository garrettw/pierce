<?php

namespace Pierce\Numerics;

class AustHex extends RFC
{
    // New numerics
    const RPL_WHOISHELPER = '309';
    const RPL_WHOISSERVICE = '310';
    const RPL_CHANNEL_URL = '328';
    const RPL_MAP = '357';
    const RPL_MAPMORE = '358';
    const RPL_MAPEND = '359';
    const RPL_SPAM = '377';
    const RPL_FORCEMOTD = '378';
    const RPL_YOURHELPER = '380';
    const RPL_NOTOPERANYMORE = '385';
    const ERR_EVENTNICKCHANGE = '430';
    const ERR_SERVICENAMEINUSE = '434';
    const ERR_NOULINE = '480';
    const ERR_VWORLDWARN = '503';
    const ERR_WHOTRUNC = '520';

    // Redefined numerics
    const RPL_BOUNCE = '005rfc';
    const RPL_SLINE = '005';
    const RPL_STATSVLINE = '240rfc';
    const RPL_STATSXLINE = '240';
    const RPL_USERIP = '307rfc';
    const RPL_SUSERHOST = '307';
    const RPL_WHOIS_HIDDEN = '320rfc';
    const RPL_WHOISVIRT = '320';
}
