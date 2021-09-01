<?php


namespace toom1996\server;


use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;
use toom1996\Application;
use toom1996\base\BaseServer;
use toom1996\base\InvalidConfigException;
use toom1996\base\UnknownClassException;
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
        'request',
    ];

    public function init()
    {
        $this->application = new \Swoole\Http\Server($this->host, $this->port);
        foreach (self::HTTP_EVENT as $event) {
            $this->application->on($event, [$this, $event]);
        }
    }

    /**
     *
     *
     * @param  Request   $request
     * @param  Response  $response
     *
     * @throws \ReflectionException
     * @throws InvalidConfigException
     * @throws UnknownClassException
     */
    public function request(Request $request, Response $response)
    {
        (new Goblin(Application::$applicationConfig, $request, $response))->run();
    }
}