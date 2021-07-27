<?php
$route = require 'route.php';
$config = [
    'id' => 'yiis',
    'basePath' => dirname(__DIR__),
    'controllersPath' => dirname(__DIR__) . '/controllers',
    'bootstrap' => ['log'],
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
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'class' => 'toom1996\web\UrlManager' ,
            'route' => [
                $route
            ],
            'rules' => [
                'GET /site/detail/<id:\d+>' => 'site/detail',
            ],
        ],
    ],
    'scanner' => [
        'attributes' => [
            'Url',
            'Xd'
        ],
    ],
];
return $config;