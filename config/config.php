<?php
/** Paths **/
//define('PATH', '//' . $_SERVER['HTTP_HOST']);

if (php_sapi_name() == 'cli') define('USERIP', getenv('HTTP_CLIENT_IP'));
else define('USERIP', $_SERVER['REMOTE_ADDR']);

/** NETup **/
define('URFA', '/netup/utm5/bin/utm5_urfaclient');
define('ADMIN_IPS', ['172.31.191.0/24']);


