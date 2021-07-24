<?php
namespace toom1996;

use Swoole\Coroutine;
use toom1996\base\Module;


/**
 * This constant defines the framework installation directory.
 */
defined('APP_PATH') or define('APP_PATH', __DIR__);

spl_autoload_register(['toom1996\Application', 'autoload'], true, true);
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
        $class = new \ReflectionClass($this);
        $b = dirname($class->getFileName());
//        var_dump($b);
        \YiiS::setAlias('@app', '/www/wwwroot/yiis-test');
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
     * @return \YiiS
     */
    public function load($app)
    {
        return new $app($this->config);
    }

    public static function autoload($className)
    {
        if (strpos($className, '\\') !== false) {
            $classFile = \YiiS::getAlias('@' . str_replace('\\', '/', $className) . '.php', false);
//            var_dump($classFile);
            if ($classFile === false || !is_file($classFile)) {
                return;
            }
        } else {
            return;
        }
//        var_dump($classFile);
        include $classFile;

//        if (YII_DEBUG && !class_exists($className, false) && !interface_exists($className, false) && !trait_exists($className, false)) {
//            throw new UnknownClassException("Unable to find '$className' in file: $classFile. Namespace missing?");
//        }
    }
}

