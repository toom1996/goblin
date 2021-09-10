<?php


namespace toom1996\http;


use Swoole\Coroutine;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Server;

class RequestCallback
{

    public static function onRequest(Request $request, Response $response)
    {
        (new Eazy(Eazy::$config, $request, $response))->run();
    }
}