<?php

$basePath = dirname(__DIR__);
$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@dist' => "{$basePath}/web/js/dist"
    ],
    'components' => [
        'assetManager' => [
            'appendTimestamp' => true,
        ],
        'request' => [
            'class' => 'Request',
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '-MWEZO3mNgjDqwQjI8X9DD6hBUGJmlBH',
        ],
        'response' => [
            'class' => 'Response'
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'class' => 'ErrorHandler',
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => 3,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'class' => 'urlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '/detail/<id:\d+>' => '/detail/',
            ],
        ],
    ],
    'params' => $params,
];


return $config;
