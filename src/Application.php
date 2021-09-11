<?php


namespace toom1996;

use toom1996\base\BaseApplication;
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
class Application extends BaseApplication
{

    public $params = [];

    public function __construct()
    {
        $this->params = $this->getParams();
        if (empty($this->params)) {
            echo "[start]  - start eazy." . PHP_EOL;
            echo "[start -d]  - nohup start eazy." . PHP_EOL;
            echo "[reload] - reload eazy." . PHP_EOL;
            echo "[stop]   - stop eazy." . PHP_EOL;
        }else{

        }
    }


    /**
     * Return swoole server.
     * @return HttpServer
     */
    public function createServer(array $config)
    {
        Eazy::setAlias('@eazy', __DIR__);
        return new Server($config);
    }
}