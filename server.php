#!/usr/bin/env php
<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/toom1996/yiis/src/http/Goblin.php';
$config = require __DIR__ . '/config/web.php';

/**
 * Defined debug mode
 */
//defined('YIIS_DEBUG') or define('YIIS_DEBUG', $config['app']['isDebug']);

ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');

error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');
$params = getParams();

if (!extension_loaded('swoole')) {
    die('The Swoole PHP extension is required by Yiis.');
}

$command = [
    1 => '  start swoole http server.',
    2 => '  stop swoole http server.',
    3 => '  reload swoole http server.',
    4 => '  yiis version',
];

if (!$params) {
    echo " \nhi guys😀 . What do you want to do? \n\n";
    foreach ($command as $key => $item) {
        echo "[$key] $item" . PHP_EOL;
    }
    echo "\n\n";

    echo "\nYour choice the one, or 'q' to quit ";
    $answer = trim(fgets(STDIN));

    if ( !in_array($answer, range(1, count($command)))) {
        if (!ctype_digit($answer)) {
            echo "\n  Quit initialization.\n";
        }else{
            echo "\n please choose right number. \n";
        }
        echo "\n console will be quit... \n ";
        exit(0);
    }
}else{
    $answer = $params[0];
}

switch ($answer) {
    case 1 :
        $http = new \Swoole\Http\Server("0.0.0.0", 9502);
        $app = new \toom1996\http\Application($config);
        $http->set(
            [
                'enable_static_handler' => true,
                'document_root' => __DIR__ . '/web',
            ]
        );
        $http->on(
            "start",
            function (\Swoole\Http\Server $http) {
                echo __DIR__;
                echo " \n YiiS is start. (●'◡'●)\n\n\n\n\n\n\n\n\n\n\n ";
            }
        );
        $http->on(
            "request",
            function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) use ($app, $http) {
//                        return $response->end("<h1>~~~~~~</h1>\n");
                $app->load(\toom1996\http\Goblin::class)->run($request, $response);
            }
        );
        break;
    case 2:
        $http = new Swoole\Http\Server('0.0.0.0', 9502);

        $http->on('request', function ($req, Swoole\Http\Response $resp) use ($http) {
            $resp->detach();
            $resp2 = Swoole\Http\Response::create($req->fd);
            $resp2->end("hello world");
        });

}

$http->start();



function getParams()
{
    $rawParams = [];
    if (isset($_SERVER['argv'])) {
        $rawParams = $_SERVER['argv'];
        array_shift($rawParams);
    }

    $params = [];
    foreach ($rawParams as $param) {
        if (preg_match('/^--([\w-]*\w)(=(.*))?$/', $param, $matches)) {
            $name = $matches[1];
            $params[$name] = isset($matches[3]) ? $matches[3] : true;
        } else {
            $params[] = $param;
        }
    }
    return $params;
}