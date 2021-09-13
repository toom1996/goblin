<?php


namespace toom1996;

use toom1996\base\BaseApplication;
use toom1996\base\InvalidConfigException;
use toom1996\helpers\Console;
use toom1996\http\Eazy;
use toom1996\http\Goblin;
use toom1996\http\UrlManager;
use toom1996\server\http\Server;
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
class Application extends BaseApplication
{
    
    public function __construct(array $config)
    {
        $this->config = $config;
        parent::__construct();
    }


    public function selectCommand()
    {
        switch ($this->params){
            case isset($this->params['start']) === true:

        }

    }

    /**
     * Return swoole server.
     * @return HttpServer
     */
    public function createServer($config)
    {
        Eazy::setAlias('@eazy', __DIR__);
        return new Server($config);
    }

    public function stopServer($pidFile)
    {
        $pid = file_get_contents($pidFile);
        var_dump($pid);
        posix_kill($pid, SIGTERM);
    }

    public function restartServer()
    {

    }
}