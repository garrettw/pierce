<?php

namespace Pierce\Numerics;

class Ircu extends RFC
{
    // New numerics
    const RPL_SNOMASK = '008';
    const RPL_STATMEMTOT = '009';
    const RPL_STATMEM = '010';
    const RPL_MAP = '015';
    const RPL_MAPMORE = '016';
    const RPL_MAPEND = '017';
    const RPL_STATSPLINE = '217';
    const RPL_STATSQLINE = '228';
    const RPL_STATSVERBOSE = '236';
    const RPL_STATSENGINE = '237';
    const RPL_STATSFLINE = '238';
    const RPL_STATSULINE = '248';
    const RPL_STATSCONN = '250';
    const RPL_PRIVS = '270';
    const RPL_SILELIST = '271';
    const RPL_ENDOFSILELIST = '272';
    const RPL_STATSDLINE = '275';
    const RPL_GLIST = '280';
    const RPL_TOPICWHOTIME = '333';
    const RPL_LISTUSAGE = '334';
    const RPL_USERIP = '340';
    const RPL_WHOSPCRPL = '354';
    const RPL_HOSTHIDDEN = '396';
    const ERR_QUERYTOOLONG = '416';
    const ERR_NICKTOOFAST = '438';
    const ERR_TARGETTOOFAST = '439';
    const ERR_NOTIMPLEMENTED = '449';
    const ERR_INVALIDUSERNAME = '468';
    const ERR_VOICENEEDED = '489';
    const ERR_NOFEATURE = '493';
    const ERR_BADFEATURE = '494';
    const ERR_BADLOGTYPE = '495';
    const ERR_BADLOGSYS = '496';
    const ERR_BADLOGVALUE = '497';
    const ERR_ISOPERLCHAN = '498';
    const ERR_SILELISTFULL = '511';
    const ERR_BADPING = '513';
    const ERR_INVALID_ERROR = '514';
    const ERR_BADEXPIRE = '515';
    const ERR_DONTCHEAT = '516';
    const ERR_DISABLED = '517';
    const ERR_LONGMASK = '518';
    const ERR_TOOMANYUSERS = '519';
    const ERR_MASKTOOWIDE = '520';
    const ERR_QUARANTINED = '524';

    // Redefined numerics
    const RPL_STATSPING = '246rfc';
    const RPL_STATSTLINE = '246';
    const RPL_STATSBLINE = '247rfc';
    const RPL_STATSGLINE = '247';
    const RPL_ACCEPTLIST = '281rfc';
    const RPL_ENDOFGLIST = '281';
    const RPL_ENDOFACCEPT = '282rfc';
    const RPL_JUPELIST = '282';
    const RPL_ALIST = '283rfc';
    const RPL_ENDOFJUPELIST = '283';
    const RPL_ENDOFALIST = '284rfc';
    const RPL_FEATURE = '284';
    const RPL_WHOWAS_TIME = '330rfc';
    const RPL_WHOISACCOUNT = '330';
    const RPL_CHANPASSOK = '338rfc';
    const RPL_WHOISACTUALLY = '338';
    const ERR_UNAVAILRESOURCE = '437rfc';
    const ERR_BANNICKCHANGE = '437';
    const ERR_NOCHANMODES = '477rfc';
    const ERR_NEEDREGGEDNICK = '477';
    const ERR_RESTRICTED = '484rfc';
    const ERR_ISCHANSERVICE = '484';
}
