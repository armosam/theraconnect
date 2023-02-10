<?php
$config = [];

if (!YII_ENV_TEST) {
    $config['controllerMap'] = [
        'fixture' => [
            'class' => 'yii\faker\FixtureController',
            'fixtureDataPath' => '@console/fixtures/data',
            'templatePath' => '@console/fixtures/templates',
            'namespace' => 'console\fixtures',
        ],
    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '192.168.50.1'],
        'generators' => [
            /*'job' => [
                'class' => \yii\queue\gii\Generator::class,
            ],*/
        ],
    ];
    $config['components'] = [
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'scriptUrl' => 'http://Connect',
            'baseUrl' => 'http://Connect',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'suffix' => null,
        ],
        'urlManagerToFront' => [
            'class' => 'yii\web\UrlManager',
            'scriptUrl' => 'http://Connect',
            'baseUrl' => 'http://Connect',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'suffix' => null,
        ]
    ];
}

return $config;