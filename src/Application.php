<?php


namespace toom1996;

use toom1996\base\InvalidConfigException;
use toom1996\http\Eazy;
use toom1996\http\Goblin;
use toom1996\http\UrlManager;
use toom1996\server\HttpServer;

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
     * Application constructor.
     *
     * @param  array  $config
     */
    public function __construct(&$config = [])
    {
        Eazy::setAlias('@goblin', __DIR__);
    }

//    public function bootstrap()
//    {
//        foreach (Application::$applicationConfig['bootstrap'] as $component) {
//            $def = self::$applicationConfig['components'][$component];
//            self::$applicationConfig['components'][$component] = Eazy::createObject($def);
//        }
//    }

    /**
     * Return swoole server.
     * @return HttpServer
     */
    public function createServer(array $config)
    {
        return new HttpServer($config);
    }
}