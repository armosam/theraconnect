<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'n8b9fROmInrKT-kNhb55NODMzQNjahB2',
            'csrfParam' => '_frontendCSRF',
        ],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '10.0.2.2', '192.168.50.1']
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
}

return $config;
