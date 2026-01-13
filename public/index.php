<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$logDir = __DIR__ . '/../var/log';
if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}
ini_set('error_log', $logDir . '/dev.log');

use NoteReact\Kernel;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

$host = $_SERVER['HTTP_HOST'] ?? 'default.local';
$site = preg_replace('/\.local$/', '', $host);
define('SITE', $site);

if (!getenv('APP_ENV')) {
    putenv('APP_ENV=dev');
}
if (!getenv('APP_DEBUG')) {
    putenv('APP_DEBUG=1');
}

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
