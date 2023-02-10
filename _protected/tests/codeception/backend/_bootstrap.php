<?php

use Codeception\Configuration;

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

defined('YII_APP_BASE_PATH') or define('YII_APP_BASE_PATH', dirname(dirname(dirname(dirname(__DIR__)))));

defined('YII_TEST_BACKEND_ENTRY_SCRIPT') or define('YII_TEST_BACKEND_ENTRY_SCRIPT',
    '/admin/'.basename(Configuration::config()['config']['test_entry_url']));

defined('YII_TEST_BACKEND_ENTRY_FILE') or define('YII_TEST_BACKEND_ENTRY_FILE', 
    YII_APP_BASE_PATH . parse_url(Configuration::config()['config']['test_entry_url'], PHP_URL_PATH));

require_once(YII_APP_BASE_PATH . '/_protected/vendor/autoload.php');
require_once(YII_APP_BASE_PATH . '/_protected/vendor/yiisoft/yii2/Yii.php');
require_once(YII_APP_BASE_PATH . '/_protected/common/config/bootstrap.php');
require_once(YII_APP_BASE_PATH . '/_protected/backend/config/bootstrap.php');

// the entry script file path for functional and acceptance tests
$_SERVER['SCRIPT_FILENAME'] = YII_TEST_BACKEND_ENTRY_FILE;
$_SERVER['SCRIPT_NAME'] = YII_TEST_BACKEND_ENTRY_SCRIPT;
$_SERVER['SERVER_NAME'] =  parse_url(Configuration::config()['config']['test_entry_url'], PHP_URL_HOST) ?: 'localhost';
$_SERVER['SERVER_PORT'] =  parse_url(Configuration::config()['config']['test_entry_url'], PHP_URL_PORT) ?: '8080';
