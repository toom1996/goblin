<?php


namespace toom1996\server\http;


use Swoole\Coroutine;
use Swoole\Database\RedisConfig;
use Swoole\Database\RedisPool;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server as swooleServer;
use Swoole\Runtime;
use toom1996\Application;
use toom1996\base\BaseServer;
use toom1996\base\Event;
use toom1996\base\InvalidConfigException;
use toom1996\base\UnknownClassException;
use toom1996\db\Redis;
use toom1996\di\Container;
use toom1996\event\SwooleEvent;
use toom1996\http\Eazy;
use toom1996\http\Goblin;
use toom1996\http\RequestCallback;
use toom1996\http\StartCallback;
use toom1996\http\UrlManager;
use toom1996\http\WorkerStartCallback;

/**
 * Class HttpServer
 *
 * @author: TOOM1996
 * @since 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class Server extends BaseServer
{
    /**
     * Defined core swoole http event.
     * It's not be overwrite.
     */
    const HTTP_EVENT = [
        SwooleEvent::SWOOLE_ON_START => [StartCallback::class, 'onStart'],
        SwooleEvent::SWOOLE_ON_REQUEST => [RequestCallback::class, 'onRequest'],
        SwooleEvent::SWOOLE_ON_WORKER_START => [WorkerStartCallback::class, 'onWorkerStart'],
    ];

    public function init()
    {
        $this->server = new swooleServer($this->host, $this->port);
        foreach (array_merge($this->event, self::HTTP_EVENT) as $event => $callback) {
            $this->server->on($event, $callback);
        }
        parent::init();
    }

    /**
     * Run swoole http server.
     */
    public function run()
    {
        $this->server->start();
    }
}