<?php
defined('YII_APP_BASE_PATH') or define('YII_APP_BASE_PATH', dirname(dirname(dirname(dirname(dirname(__DIR__))))));

/**
 * Application configuration for console unit tests
 */
return yii\helpers\ArrayHelper::merge(
    require(YII_APP_BASE_PATH . '/_protected/common/config/main.php'),
    require(YII_APP_BASE_PATH . '/_protected/common/config/main-local.php'),
    require(YII_APP_BASE_PATH . '/_protected/console/config/main.php'),
    require(YII_APP_BASE_PATH . '/_protected/console/config/main-local.php'),
    require(dirname(__DIR__) . '/config.php'),
    require(dirname(__DIR__) . '/unit.php'),
    require(__DIR__ . '/config.php'),
    [
    ]
);
