<?php
defined('YII_APP_BASE_PATH') or define('YII_APP_BASE_PATH', dirname(dirname(dirname(dirname(dirname(__DIR__))))));

$_SERVER['SCRIPT_FILENAME'] = YII_TEST_FRONTEND_ENTRY_FILE;
$_SERVER['SCRIPT_NAME'] = YII_TEST_FRONTEND_ENTRY_SCRIPT;

/**
 * Application configuration for frontend functional tests
 */
return yii\helpers\ArrayHelper::merge(
    require(YII_APP_BASE_PATH . '/_protected/common/config/main.php'),
    require(YII_APP_BASE_PATH . '/_protected/common/config/main-local.php'),
    require(YII_APP_BASE_PATH . '/_protected/frontend/config/main.php'),
    require(YII_APP_BASE_PATH . '/_protected/frontend/config/main-local.php'),
    require(dirname(__DIR__) . '/config.php'),
    require(dirname(__DIR__) . '/functional.php'),
    require(__DIR__ . '/config.php'),
    [
    ]
);
