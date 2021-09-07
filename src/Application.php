<?php


namespace toom1996;

use toom1996\base\InvalidConfigException;
use toom1996\http\Eazy;
use toom1996\http\Goblin;
use toom1996\http\UrlManager;
use toom1996\server\http\Server;

/**
 * This constant defines the framework installation directory.
 */
defined('EAZY_PATH') or define('EAZY_PATH', __DIR__);

/**
 * Class Application
 *
 * @author: TOOM1996
 */
class Application
{

    /**
     * Return swoole server.
     * @return HttpServer
     */
    public function createServer(array $config)
    {
        return new Server($config);
    }
}