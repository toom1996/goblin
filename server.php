#!/usr/bin/env php
<?php

declare(strict_types=1);

use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;
$http = new Server("0.0.0.0", 9502);
$config = require 'web.php';
$app = new \toom1996\YiiS($config);
$http->on(
    "start",
    function (Server $http) {
        echo "Swoole HTTP server is started.\n";
    }
);
$http->set([
    'worker_num' => 4,
]);
$arr = [];
$http->on(
    "request",
    function (Request $request, Response $response) use ($app) {
//        var_dump($request->fd);
//        $response->end("<h1>~~~~~~</h1>\n");
        $app->load(\toom1996\Applicaiton::class)->run($request, $response);
//            (new Toom())->run($request, $response);
//            $app->run($request, $response);
//        $_params_ = ['a' => 1];
//        $info = pathinfo('../views/test.php');
//        var_dump(pathinfo('../views/test.php', PATHINFO_EXTENSION));
//        var_dump(pathinfo('../views/test.php', PATHINFO_EXTENSION));
//        var_dump(APP_PATH);
//        $_obInitialLevel_ = ob_get_level();
//        ob_start();
//        ob_implicit_flush(false);
//        extract($_params_, EXTR_OVERWRITE);
//        try {
//            require 'views/test.php';
//            return $response->end(ob_get_clean());
//        } catch (\Exception $e) {
//            while (ob_get_level() > $_obInitialLevel_) {
//                if (!@ob_end_clean()) {
//                    ob_clean();
//                }
//            }
//            return $response->end($e->getMessage());
//            throw $e;
//        } catch (\Throwable $e) {
//            while (ob_get_level() > $_obInitialLevel_) {
//                if (!@ob_end_clean()) {
//                    ob_clean();
//                }
//            }
//            return $response->end($e->getMessage());
//            throw $e;
//        }
//        $response->end("<h1>~~~~~~</h1>\n");
    }
);

$http->on('close', function ($sv, $fd) {
    // 处理连接关闭事件
    // 连接编号 $fd
    // 连接关闭时删除连接对应的全局数组
    // unset($GLOBALS['app']['swoole'][$fd]);

    var_dump($fd);
});

$http->start();
