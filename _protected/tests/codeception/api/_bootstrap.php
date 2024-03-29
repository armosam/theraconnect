<?php

use Codeception\Configuration;

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

defined('YII_APP_BASE_PATH') or define('YII_APP_BASE_PATH', dirname(dirname(dirname(dirname(__DIR__)))));

defined('YII_TEST_API_ENTRY_URL') or define('YII_TEST_API_ENTRY_URL',
    parse_url(Configuration::config()['config']['test_entry_url'], PHP_URL_PATH));

defined('YII_TEST_API_ENTRY_FILE') or define('YII_TEST_API_ENTRY_FILE', 
    YII_APP_BASE_PATH . YII_TEST_API_ENTRY_URL);

require_once(YII_APP_BASE_PATH . '/_protected/vendor/autoload.php');
require_once(YII_APP_BASE_PATH . '/_protected/vendor/yiisoft/yii2/Yii.php');
require_once(YII_APP_BASE_PATH . '/_protected/common/config/bootstrap.php');
require_once(YII_APP_BASE_PATH . '/_protected/api/config/bootstrap.php');

// set correct script paths

// the entry script file path for functional and acceptance tests
$_SERVER['SCRIPT_FILENAME'] = YII_TEST_API_ENTRY_FILE;
$_SERVER['SCRIPT_NAME'] = YII_TEST_API_ENTRY_URL;
$_SERVER['SERVER_NAME'] =  parse_url(Configuration::config()['config']['test_entry_url'], PHP_URL_HOST) ?: 'localhost';
$_SERVER['SERVER_PORT'] =  parse_url(Configuration::config()['config']['test_entry_url'], PHP_URL_PORT) ?: '8080';

Yii::setAlias('@tests', dirname(dirname(__DIR__)));
