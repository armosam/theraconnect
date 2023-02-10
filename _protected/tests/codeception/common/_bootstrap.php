<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

defined('YII_APP_BASE_PATH') or define('YII_APP_BASE_PATH', dirname(dirname(dirname(dirname(__DIR__)))));

require_once(YII_APP_BASE_PATH . '/_protected/vendor/autoload.php');
require_once(YII_APP_BASE_PATH . '/_protected/vendor/yiisoft/yii2/Yii.php');
require_once(YII_APP_BASE_PATH . '/_protected/common/config/bootstrap.php');

// set correct script paths
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['SERVER_PORT'] = '8080';

