<?php
define('ROOT', __DIR__);
require_once 'config/config.php';
if (php_sapi_name() === 'cli') define('ARGV', $argv);
// подключение файла конфигурации

spl_autoload_register(function ($class_name) {
    $load = false;
    if (file_exists(__DIR__ . '/core/' . $class_name . '.php')) {require_once __DIR__ . '/core/' . $class_name . '.php';$load = true;}
    if (file_exists(__DIR__ . '/controllers/' . $class_name . '.php')) {require_once __DIR__ . '/controllers/' . $class_name . '.php';$load = true;}
    if (file_exists(__DIR__ . '/models/' . $class_name . '.php')) {require_once __DIR__ . '/models/' . $class_name . '.php';$load = true;}
    if (!$load) header("HTTP/1.0 404 Not Found");
});

$app = new Bootstrap();
