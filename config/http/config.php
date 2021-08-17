<?php
$swoole = __DIR__ . 'swoole.php';
$route = __DIR__ . 'route.php';

return [
    // Application id.
    'id' => 'goblin-http-application',
    // Application path.
    'basePath' => dirname(__DIR__),
    // Controller path.
    // The directory where the controller files are placed.
    'controllersPath' => dirname(__DIR__) . '/controllers',
    'swoole' => $swoole,
    // Aliases.
    'aliases' => [
    ],
    'components' => [
        // If catch error, will use the `errorAction` method for processing.
        'errorHandler' => [
            'errorAction' => '@controllers/site/error',
        ],
    ],
];