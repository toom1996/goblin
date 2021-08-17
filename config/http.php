<?php
$route = require 'route.php';
return [
    // Application id.
    'id' => 'goblin-http-application',
    // Application path.
    'basePath' => dirname(__DIR__),
    // Controller path.
    // The directory where the controller files are placed.
    'controllersPath' => dirname(__DIR__) . '/controllers',
    'http' => [
        'host' => "0.0.0.0",
        'port' => 9502,
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'assetManager' => [
            'appendTimestamp' => true,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => '@controllers/site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'class' => 'toom1996\http\UrlManager' ,
            'route' => $route
        ],
    ],
    'scanner' => [
        'attributes' => [
            'Url',
            'Xd'
        ],
    ],
];