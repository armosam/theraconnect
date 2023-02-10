<?php
defined('YII_APP_BASE_PATH') or define('YII_APP_BASE_PATH', dirname(dirname(dirname(dirname(dirname(__DIR__))))));

$_SERVER['SCRIPT_FILENAME'] = YII_TEST_API_ENTRY_FILE;
$_SERVER['SCRIPT_NAME'] = YII_TEST_API_ENTRY_URL;

/**
 * Application configuration for api functional tests
 */
return yii\helpers\ArrayHelper::merge(
    require(YII_APP_BASE_PATH . '/_protected/common/config/main.php'),
    require(YII_APP_BASE_PATH . '/_protected/common/config/main-local.php'),
    require(YII_APP_BASE_PATH . '/_protected/api/config/main.php'),
    require(YII_APP_BASE_PATH . '/_protected/api/config/main-local.php'),
    require(dirname(__DIR__) . '/config.php'),
    require(dirname(__DIR__) . '/api.php'),
    require(__DIR__ . '/config.php'),
    [
    ]
);
