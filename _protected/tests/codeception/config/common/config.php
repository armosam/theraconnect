<?php

/**
 * We need params for common for unit tests
 * @var array $params
 */
$params = yii\helpers\ArrayHelper::merge(
    require(YII_APP_BASE_PATH . '/_protected/common/config/params.php'),
    require(YII_APP_BASE_PATH . '/_protected/common/config/params-local.php')
);

/**
 * Application configuration for all common test types
 */
return [
    'id' => 'app-console-tests',
    'basePath' => dirname(__DIR__),
    'runtimePath' => Yii::getAlias('@frontend/runtime'),
    'params' => $params
];