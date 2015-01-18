<?php

namespace Pierce\Numerics;

class Unreal extends RFC
{
    // New numerics
    const RPL_MAP = '006';
    const RPL_MAPEND = '007';
    const RPL_STATSOLDNLINE = '214';
    const RPL_STATSBLINE = '220';
    const RPL_STATSGLINE = '223';
    const RPL_STATSTLINE = '224';
    const RPL_STATSELINE = '225';
    const RPL_STATSNLINE = '226';
    const RPL_STATSVLINE = '227';
    const RPL_RULES = '232';
    const RPL_STATSXLINE = '247';
    const RPL_HELPHDR = '290';
    const RPL_HELPOP = '291';
    const RPL_HELPTLR = '292';
    const RPL_HELPHLP = '293';
    const RPL_HELPFWD = '294';
    const RPL_HELPIGN = '295';
    const RPL_RULESSTART = '308';
    const RPL_ENDOFRULES = '309';
    const RPL_WHOISHELPOP = '310';
    const RPL_WHOISSPECIAL = '320';
    const RPL_LISTSYNTAX = '334';
    const RPL_WHOISBOT = '335';
    const RPL_WHOISHOST = '378';
    const RPL_WHOISMODES = '379';
    const RPL_NOTOPERANYMORE = '385';
    const RPL_QLIST = '386';
    const RPL_ENDOFQLIST = '387';
    const RPL_ALIST = '388';
    const RPL_ENDOFALIST = '389';
    const ERR_NOOPERMOTD = '425';
    const ERR_NORULES = '434';
    const ERR_SERVICECONFUSED = '435';
    const ERR_NCHANGETOOFAST = '438';
    const ERR_SERVICESDOWN = '440';
    const ERR_NONICKCHANGE = '447';
    const ERR_HOSTILENAME = '455';
    const ERR_NOHIDING = '459';
    const ERR_NOTFORHALFOPS = '460';
    const ERR_ONLYSERVERSCANCHANGE = '468';
    const ERR_LINKSET = '469';
    const ERR_LINKCHANNEL = '470';
    const ERR_LINKFAIL = '479';
    const ERR_CANNOTKNOCK = '480';
    const ERR_SECUREONLYCHAN = '489';
    const ERR_CHANOWNPRIVNEEDED = '499';
    const ERR_NEEDPONG = '513';
    const ERR_NOINVITE = '518';
    const ERR_ADMONLY = '519';
    const ERR_OPERONLY = '520';
    const ERR_OPERSPVERIFY = '524';
    const RPL_LOGON = '600';
    const RPL_LOGOFF = '601';
    const RPL_WATCHOFF = '602';
    const RPL_WATCHSTAT = '603';
    const RPL_NOWON = '604';
    const RPL_NOWOFF = '605';
    const RPL_WATCHLIST = '606';
    const RPL_ENDOFWATCHLIST = '607';
    const RPL_MAPMORE = '610';
    const ERR_CANNOTDOCOMMAND = '972';
    const ERR_NUMERICERR = '999';

    // Redefined numerics
    const RPL_BOUNCE = '005rfc';
    const RPL_PROTOCTL = '005';
    const RPL_MODLIST = '222rfc';
    const RPL_SQLINE_NICK = '222';
    const RPL_STATSDLINE = '250rfc';
    const RPL_STATSCONN = '250';
    const RPL_USERIP = '307rfc';
    const RPL_WHOISREGNICK = '307';
    const RPL_EXCEPTLIST = '348rfc';
    const RPL_EXLIST = '348';
    const RPL_ENDOFEXCEPTLIST = '349rfc';
    const RPL_ENDOFEXLIST = '349';
    const ERR_NOCHANMODES = '477rfc';
    const ERR_NEEDREGGEDNICK = '477';
    const ERR_RESTRICTED = '484rfc';
    const ERR_ATTACKDENY = '484';
    const ERR_UNIQOPRIVSNEEDED = '485rfc';
    const ERR_KILLDENY = '485';
    const ERR_NONONREG = '486rfc';
    const ERR_HTMDISABLED = '486';
}
