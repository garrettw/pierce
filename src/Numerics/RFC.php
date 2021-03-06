<?php

namespace Pierce\Numerics;

class RFC
{
    const RPL_WELCOME = '001';
    const RPL_YOURHOST = '002';
    const RPL_CREATED = '003';
    const RPL_MYINFO = '004';
    const RPL_BOUNCE = '005';
    const RPL_YOURID = '042';
    const RPL_SAVENICK = '043';
    const RPL_TRACELINK = '200';
    const RPL_TRACECONNECTING = '201';
    const RPL_TRACEHANDSHAKE = '202';
    const RPL_TRACEUNKNOWN = '203';
    const RPL_TRACEOPERATOR = '204';
    const RPL_TRACEUSER = '205';
    const RPL_TRACESERVER = '206';
    const RPL_TRACESERVICE = '207';
    const RPL_TRACENEWTYPE = '208';
    const RPL_TRACECLASS = '209';
    const RPL_TRACERECONNECT = '210';
    const RPL_STATSLINKINFO = '211';
    const RPL_STATSCOMMANDS = '212';
    const RPL_STATSCLINE = '213';
    const RPL_STATSNLINE = '214';
    const RPL_STATSILINE = '215';
    const RPL_STATSKLINE = '216';
    const RPL_STATSQLINE = '217';
    const RPL_STATSYLINE = '218';
    const RPL_ENDOFSTATS = '219';
    const RPL_UMODEIS = '221';
    const RPL_SERVICEINFO = '231';
    const RPL_ENDOFSERVICES = '232';
    const RPL_SERVICE = '233';
    const RPL_SERVLIST = '234';
    const RPL_SERVLISTEND = '235';
    const RPL_STATSIAUTH = '239';
    const RPL_STATSVLINE = '240';
    const RPL_STATSLLINE = '241';
    const RPL_STATSUPTIME = '242';
    const RPL_STATSOLINE = '243';
    const RPL_STATSHLINE = '244';
    const RPL_STATSSLINE = '245';
    const RPL_STATSPING = '246';
    const RPL_STATSBLINE = '247';
    const RPL_STATSULINE = '249';
    const RPL_STATSDLINE = '250';
    const RPL_LUSERCLIENT = '251';
    const RPL_LUSEROP = '252';
    const RPL_LUSERUNKNOWN = '253';
    const RPL_LUSERCHANNELS = '254';
    const RPL_LUSERME = '255';
    const RPL_ADMINME = '256';
    const RPL_ADMINLOC1 = '257';
    const RPL_ADMINLOC2 = '258';
    const RPL_ADMINEMAIL = '259';
    const RPL_TRACELOG = '261';
    const RPL_TRACEEND = '262';
    const RPL_TRYAGAIN = '263';
    const RPL_LOCALUSERS = '265';
    const RPL_GLOBALUSERS = '266';
    const RPL_VCHANEXIST = '276';
    const RPL_VCHANLIST = '277';
    const RPL_VCHANHELP = '278';
    const RPL_ACCEPTLIST = '281';
    const RPL_ENDOFACCEPT = '282';
    const RPL_ALIST = '283';
    const RPL_ENDOFALIST = '284';
    const RPL_GLIST_HASH = '285';
    const RPL_NONE = '300';
    const RPL_AWAY = '301';
    const RPL_USERHOST = '302';
    const RPL_ISON = '303';
    const RPL_TEXT = '304';
    const RPL_UNAWAY = '305';
    const RPL_NOWAWAY = '306';
    const RPL_USERIP = '307';
    const RPL_WHOISUSER = '311';
    const RPL_WHOISSERVER = '312';
    const RPL_WHOISOPERATOR = '313';
    const RPL_WHOWASUSER = '314';
    const RPL_ENDOFWHO = '315';
    const RPL_WHOISCHANOP = '316';
    const RPL_WHOISIDLE = '317';
    const RPL_ENDOFWHOIS = '318';
    const RPL_WHOISCHANNELS = '319';
    const RPL_WHOIS_HIDDEN = '320';
    const RPL_LIST = '322';
    const RPL_LISTEND = '323';
    const RPL_CHANNELMODEIS = '324';
    const RPL_UNIQOPIS = '325';
    const RPL_NOCHANPASS = '326';
    const RPL_CHPASSUNKNOWN = '327';
    const RPL_CREATIONTIME = '329';
    const RPL_WHOWAS_TIME = '330';
    const RPL_NOTOPIC = '331';
    const RPL_TOPIC = '332';
    const RPL_CHANPASSOK = '338';
    const RPL_BADCHANPASS = '339';
    const RPL_INVITING = '341';
    const RPL_SUMMONING = '342';
    const RPL_INVITED = '345';
    const RPL_INVITELIST = '346';
    const RPL_ENDOFINVITELIST = '347';
    const RPL_EXCEPTLIST = '348';
    const RPL_ENDOFEXCEPTLIST = '349';
    const RPL_VERSION = '351';
    const RPL_WHOREPLY = '352';
    const RPL_NAMREPLY = '353';
    const RPL_KILLDONE = '361';
    const RPL_CLOSING = '362';
    const RPL_CLOSEEND = '363';
    const RPL_LINKS = '364';
    const RPL_ENDOFLINKS = '365';
    const RPL_ENDOFNAMES = '366';
    const RPL_BANLIST = '367';
    const RPL_ENDOFBANLIST = '368';
    const RPL_ENDOFWHOWAS = '369';
    const RPL_INFO = '371';
    const RPL_MOTD = '372';
    const RPL_INFOSTART = '373';
    const RPL_ENDOFINFO = '374';
    const RPL_MOTDSTART = '375';
    const RPL_ENDOFMOTD = '376';
    const RPL_YOUREOPER = '381';
    const RPL_REHASHING = '382';
    const RPL_YOURESERVICE = '383';
    const RPL_MYPORTIS = '384';
    const RPL_TIME = '391';
    const RPL_USERSSTART = '392';
    const RPL_USERS = '393';
    const RPL_ENDOFUSERS = '394';
    const RPL_NOUSERS = '395';
    const ERR_UNKNOWNERROR = '400';
    const ERR_NOSUCHNICK = '401';
    const ERR_NOSUCHSERVER = '402';
    const ERR_NOSUCHCHANNEL = '403';
    const ERR_CANNOTSENDTOCHAN = '404';
    const ERR_TOOMANYCHANNELS = '405';
    const ERR_WASNOSUCHNICK = '406';
    const ERR_TOOMANYTARGETS = '407';
    const ERR_NOSUCHSERVICE = '408';
    const ERR_NOORIGIN = '409';
    const ERR_NORECIPIENT = '411';
    const ERR_NOTEXTTOSEND = '412';
    const ERR_NOTOPLEVEL = '413';
    const ERR_WILDTOPLEVEL = '414';
    const ERR_BADMASK = '415';
    const ERR_UNKNOWNCOMMAND = '421';
    const ERR_NOMOTD = '422';
    const ERR_NOADMININFO = '423';
    const ERR_FILEERROR = '424';
    const ERR_NONICKNAMEGIVEN = '431';
    const ERR_ERRONEUSNICKNAME = '432';
    const ERR_NICKNAMEINUSE = '433';
    const ERR_NICKCOLLISION = '436';
    const ERR_UNAVAILRESOURCE = '437';
    const ERR_USERNOTINCHANNEL = '441';
    const ERR_NOTONCHANNEL = '442';
    const ERR_USERONCHANNEL = '443';
    const ERR_NOLOGIN = '444';
    const ERR_SUMMONDISABLED = '445';
    const ERR_USERSDISABLED = '446';
    const ERR_NOTREGISTERED = '451';
    const ERR_IDCOLLISION = '452';
    const ERR_NICKLOST = '453';
    const ERR_ACCEPTFULL = '456';
    const ERR_ACCEPTEXIST = '457';
    const ERR_ACCEPTNOT = '458';
    const ERR_NEEDMOREPARAMS = '461';
    const ERR_ALREADYREGISTERED = '462';
    const ERR_NOPERMFORHOST = '463';
    const ERR_PASSWDMISMATCH = '464';
    const ERR_YOUREBANNEDCREEP = '465';
    const ERR_YOUWILLBEBANNED = '466';
    const ERR_KEYSET = '467';
    const ERR_CHANNELISFULL = '471';
    const ERR_UNKNOWNMODE = '472';
    const ERR_INVITEONLYCHAN = '473';
    const ERR_BANNEDFROMCHAN = '474';
    const ERR_BADCHANNELKEY = '475';
    const ERR_BADCHANMASK = '476';
    const ERR_NOCHANMODES = '477';
    const ERR_BANLISTFULL = '478';
    const ERR_NOPRIVILEGES = '481';
    const ERR_CHANOPRIVSNEEDED = '482';
    const ERR_CANTKILLSERVER = '483';
    const ERR_RESTRICTED = '484';
    const ERR_UNIQOPRIVSNEEDED = '485';
    const ERR_NONONREG = '486';
    const ERR_TSLESSCHAN = '488';
    const ERR_NOOPERHOST = '491';
    const ERR_NOSERVICEHOST = '492';
    const ERR_UMODEUNKNOWNFLAG = '501';
    const ERR_USERSDONTMATCH = '502';
    const ERR_USERNOTONSERV = '504';
    const RPL_TRACEROUTE_HOP = '660';
    const RPL_TRACEROUTE_START = '661';
    const RPL_MODECHANGEWARN = '662';
    const RPL_CHANREDIR = '663';
    const RPL_SERVMODEIS = '664';
    const RPL_OTHERUMODEIS = '665';
    const RPL_ENDOF_GENERIC = '666';
    const RPL_WHOWASDETAILS = '670';
    const RPL_WHOISSECURE = '671';
    const RPL_UNKNOWNMODES = '672';
    const RPL_CANNOTSETMODES = '673';
    const RPL_LUSERSTAFF = '678';
    const RPL_TIMEONSERVERIS = '679';
    const RPL_NETWORKS = '682';
    const RPL_YOURLANGUAGEIS = '687';
    const RPL_LANGUAGE = '688';
    const RPL_WHOISSTAFF = '689';
    const RPL_WHOISLANGUAGE = '690';
    const RPL_MODLIST = '702';
    const RPL_ENDOFMODLIST = '703';
    const RPL_HELPSTART = '704';
    const RPL_HELPTXT = '705';
    const RPL_ENDOFHELP = '706';
    const RPL_ETRACEFULL = '708';
    const RPL_ETRACE = '709';
    const RPL_KNOCK = '710';
    const RPL_KNOCKDLVR = '711';
    const ERR_TOOMANYKNOCK = '712';
    const ERR_CHANOPEN = '713';
    const ERR_KNOCKONCHAN = '714';
    const ERR_KNOCKDISABLED = '715';
    const RPL_TARGUMODEG = '716';
    const RPL_TARGNOTIFY = '717';
    const RPL_UMODEGMSG = '718';
    const RPL_OMOTDSTART = '720';
    const RPL_OMOTD = '721';
    const RPL_OMOTDEND = '722';
    const ERR_NOPRIVS = '723';
    const RPL_TESTMARK = '724';
    const RPL_TESTLINE = '725';
    const RPL_NOTESTLINE = '726';
    const RPL_XINFO = '771';
    const RPL_XINFOSTART = '773';
    const RPL_XINFOEND = '774';
    const ERR_CANNOTCHANGEUMODE = '973';
    const ERR_CANNOTCHANGECHANMODE = '974';
    const ERR_CANNOTCHANGESERVERMODE = '975';
    const ERR_CANNOTSENDTONICK = '976';
    const ERR_UNKNOWNSERVERMODE = '977';
    const ERR_SERVERMODELOCK = '979';
    const ERR_BADCHARENCODING = '980';
    const ERR_TOOMANYLANGUAGES = '981';
    const ERR_NOLANGUAGE = '982';
    const ERR_TEXTTOOSHORT = '983';

    private $code = [];

    public function __construct()
    {
        $this->code = array_flip((new \ReflectionClass(get_class($this)))->getConstants());
    }

    public function __get($name)
    {
        return $this->$name;
    }
}
