<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'homeUrl' => '/admin/home',
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'assetManager' => [
            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-blue-light',
                ],
            ],
        ],
        // here you can set theme used for your backend application 
        // - template comes with: 'thera', 'default', 'slate', 'spacelab', 'flatly' and 'cerulean'
        'view' => [
            'theme' => [
                'basePath' => '@webroot/themes/thera',
                'baseUrl' => '@web/themes/thera',
                'pathMap' => [
                    '@app/views' => '@webroot/themes/thera/views'
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\base\UserIdentity',
            'enableAutoLogin' => true,
            'enableSession' => true,
            'authTimeout' => 7200,
            'identityCookie' => [
                'name' => '_backendUser',
                'httpOnly' => true,
                'expire' => 7200,
            ]
        ],
        'session' => [
            'name' => 'PHPBACKSESSID',
            'savePath' => sys_get_temp_dir(),
            'timeout' => 7200,
        ],
        /*'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],*/
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'params' => $params,
    'aliases' => [
        '@theme' => '/admin/themes/thera',
        '@webroot/theme' => dirname(dirname(dirname(__DIR__)))  . '/admin/themes/thera',
    ]
];
