<?php

use common\models\Language;

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'homeUrl' => '/home',
    'bootstrap' => ['log', /*'languagepicker'*/],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        // here you can set theme used for your frontend application 
        // - template comes with: 'default', 'slate', 'spacelab', 'cerulean', 'flatly' and 'thera'
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
            'authTimeout' => 10800,
            'identityCookie' => [
                'name' => '_frontendUser',
                'httpOnly' => true,
                'expire' => 10800,
            ]
        ],
        'session' => [
            'name' => 'PHPFRONTSESSID',
            'savePath' => sys_get_temp_dir(),
            'timeout' => 10800,
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
//        'languagepicker' => [
//            'class' => '\common\components\LanguagePicker',
//            'cookieName' => 'language_picker',
//            'expireDays' => 10,
//            'languages' => function () {
//                // List of available languages (icons and text: en-US => English ... etc.)
//                return Language::getLanguageList();
//            },
//            /*'callback' => function() {
//                if (!\Yii::$app->user->isGuest) {
//                    $user = \Yii::$app->user->identity;
//                    $user->language = \Yii::$app->language;
//                    $user->save();
//                }
//            }*/
//        ],
    ],
    'params' => $params,
    'aliases' => [
        '@theme' => '/themes/thera',
        '@webroot/theme' => dirname(dirname(dirname(__DIR__)))  . '/web/themes/thera',
    ]
];
