<?php

namespace Pierce\Numerics;

class Aircd extends RFC
{
    // New numerics
    const RPL_ATTEMPTINGJUNC = '050';
    const RPL_ATTEMPTINGREROUTE = '051';
    const RPL_LOCALUSERS = '265';
    const RPL_GLOBALUSERS = '266';
    const RPL_START_NETSTAT = '267';
    const RPL_NETSTAT = '268';
    const RPL_END_NETSTAT = '269';
    const RPL_NOTIFY = '273';
    const RPL_ENDNOTIFY = '274';
    const RPL_CHANINFO_HANDLE = '285';
    const RPL_CHANINFO_USERS = '286';
    const RPL_CHANINFO_CHOPS = '287';
    const RPL_CHANINFO_VOICES = '288';
    const RPL_CHANINFO_AWAY = '289';
    const RPL_CHANINFO_OPERS = '290';
    const RPL_CHANINFO_BANNED = '291';
    const RPL_CHANINFO_BANS = '292';
    const RPL_CHANINFO_INVITE = '293';
    const RPL_CHANINFO_INVITES = '294';
    const RPL_CHANINFO_KICK = '295';
    const RPL_CHANINFO_KICKS = '296';
    const RPL_END_CHANINFO = '299';
    const RPL_NOTIFYACTION = '308';
    const RPL_NICKTRACE = '309';
    const RPL_KICKEXPIRED = '377';
    const RPL_BANEXPIRED = '378';
    const RPL_KICKLINKED = '379';
    const RPL_BANLINKED = '380';
    const ERR_LENGTHTRUNCATED = '419';
    const ERR_KICKEDFROMCHAN = '470';
    const ERR_NOTIFYFULL = '512';

    // Redefined numerics
    const RPL_TRACERECONNECT = '210rfc';
    const RPL_STATS = '210';
}
