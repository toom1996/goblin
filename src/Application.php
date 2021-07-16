<?php

namespace toom1996;

use Swoole\Coroutine;
use toom1996\base\Module;

/**
 * This constant defines the framework installation directory.
 */
defined('APP_PATH') or define('APP_PATH', __DIR__);

/**
 * Class BaseApplication
 *
 * @author: TOOM <1023150697@qq.com>
 */
class Application
{
    public $config;

    public static $_config;

    protected static $pool;

    public static $coroutineContainer = [];


    public function __construct($config = [])
    {
        $this->config = $config;
        self::$_config = $config;
    }

    public static function get($key)
    {
        $cid = Coroutine::getuid();
        if ($cid < 0) {
            return null;
        }
        if(isset(self::$pool[$cid][$key])){
            return self::$pool[$cid][$key];
        }
        return null;
    }

    public function put($key, $item)
    {
        $cid = Coroutine::getuid();
        if ($cid > 0)
        {
            self::$pool[$cid][$key] = $item;
        }

    }

    public function getConfig()
    {
        return $this->config;
    }

    public static function config()
    {
        return self::$_config;
    }

    /**
     *
     * @param $app
     *
     * @return \toom1996\Applicaiton
     */
    public function load($app)
    {
        return new $app($this->config);
    }


}