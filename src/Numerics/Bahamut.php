<?php

namespace Pierce\Numerics;

class Bahamut extends RFC
{
    // New numerics
    const RPL_STATSBLINE = '220|222';
    const RPL_STATSELINE = '223';
    const RPL_STATSFLINE = '224';
    const RPL_STATSZLINE = '225';
    const RPL_STATSCOUNT = '226';
    const RPL_STATSGLINE = '227';
    const RPL_WHOISADMIN = '308';
    const RPL_WHOISSADMIN = '309';
    const RPL_WHOISSVCMSG = '310';
    const RPL_CHANNEL_URL = '328';
    const RPL_COMMANDSYNTAX = '334';
    const ERR_BANONCHAN = '435';
    const ERR_SERVICESDOWN = '440';
    const ERR_ONLYSERVERSCANCHANGE = '468';
    const ERR_MSGSERVICES = '487';
    const ERR_TOOMANYWATCH = '512';
    const ERR_TOOMANYDCC = '514';
    const ERR_LISTSYNTAX = '521';
    const ERR_WHOSYNTAX = '522';
    const ERR_WHOLIMEXCEED = '523';
    const RPL_LOGON = '600';
    const RPL_LOGOFF = '601';
    const RPL_WATCHOFF = '602';
    const RPL_WATCHSTAT = '603';
    const RPL_NOWON = '604';
    const RPL_NOWOFF = '605';
    const RPL_WATCHLIST = '606';
    const RPL_ENDOFWATCHLIST = '607';
    const RPL_DCCSTATUS = '617';
    const RPL_DCCLIST = '618';
    const RPL_ENDOFDCCLIST = '619';
    const RPL_DCCINFO = '620';
    const ERR_NUMERIC_ERR = '999';

    // Redefined numerics
    const RPL_BOUNCE = '005rfc';
    const RPL_PROTOCTL = '005';
    const RPL_USERIP = '307rfc';
    const RPL_WHOISREGNICK = '307';
    const RPL_CHANPASSOK = '338rfc';
    const RPL_WHOISACTUALLY = '338';
    const ERR_NOSUCHSERVICE = '408rfc';
    const ERR_NOCOLORSONCHAN = '408';
    const ERR_TOOMANYAWAY = '429';
    const ERR_NOCHANMODES = '477rfc';
    const ERR_NEEDREGGEDNICK = '477';
    const ERR_RESTRICTED = '484rfc';
    const ERR_DESYNC = '484';
}
