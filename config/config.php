<?php
/** Paths **/
//define('PATH', '//' . $_SERVER['HTTP_HOST']);

if (php_sapi_name() == 'cli') define('USER_IP', getenv('HTTP_CLIENT_IP'));
else define('USER_IP', $_SERVER['REMOTE_ADDR']);

/** NETup **/
const ADMIN_IPS = ['172.24.130.0/24', '172.29.128.0/24'];
const LOGS = '/var/log/urfa';
const APIS = [
    'webutm_new' => 'eu0UBooghuokahphiel4',
    'webutm' => 'faiJaipeez9Ohneg4iec',
    'lichcab' => 'neeCh3Beehoongabeefe',
    '24oko' => 'Noxaigh0aighadaik0ku',
];


