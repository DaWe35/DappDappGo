<?php
define('DEBUG_MODE', false);

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'dappdappgo');
define('DB_USER', '');
define('DB_PASSWD', '');

define('URL','http://ddg.local/');

if (DEBUG_MODE == true) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}