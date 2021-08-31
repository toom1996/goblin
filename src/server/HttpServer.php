<?php


namespace toom1996\server;


use Swoole\Http\Server;
use toom1996\Application;
use toom1996\base\BaseServer;
use toom1996\GoblinLoader;
use toom1996\http\Goblin;

/**
 * Class HttpServer
 *
 * @author: TOOM1996
 */
class HttpServer extends BaseServer
{
    const HTTP_EVENT = [
        'start',
//        'request',
    ];

    public function init()
    {
        $http = new \Swoole\Http\Server($this->host, $this->port);
        foreach (self::HTTP_EVENT as $event) {
            $http->on($event, call_user_func([$this, $event]));
        }
    }



}