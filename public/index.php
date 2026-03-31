<?php
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');

// Autoload
require_once ROOT_PATH . '/vendor/autoload.php';

// Config
$config = require CONFIG_PATH . '/app.php';

// Router
require_once ROOT_PATH . '/routes/web.php';

