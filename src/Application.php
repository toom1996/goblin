<?php
namespace toom1996;

use Swoole\Coroutine;
use toom1996\base\Module;
use toom1996\base\UnknownClassException;


/**
 * This constant defines the framework installation directory.
 */
defined('APP_PATH') or define('APP_PATH', __DIR__);

/**
 * Register autoload function.
 */
spl_autoload_register(['toom1996\Application', 'autoload'], true, true);
/**
 * Base Application.
 * Defined all public variables.
 *
 * @author: TOOM <1023150697@qq.com>
 */
class Application
{
    /**
     * Application config.
     * If the config/web.php content has changed, this variables cannot be changed accordingly.
     * @var array 
     */
    public $config;
    
    public function __construct($config = [])
    {
        $this->config = $config;
        \YiiS::setAlias('@app', $config['basePath']);
    }
    
    /**
     * Load a new YIIS object from application.
     *
     * @param $app
     *
     * @return \YiiS
     */
    public function load($app)
    {
        return new $app($this->config);
    }

    /**
     * YiiS autoload function.
     * When Application initial, register this function for `spl_autoload_register` callback function.
     * @param  string  $className
     */
    public static function autoload(string $className): void
    {
        if (strpos($className, '\\') !== false) {
            $classFile = \YiiS::getAlias('@' . str_replace('\\', '/', $className) . '.php', false);
            if ($classFile === false || !is_file($classFile)) {
                return;
            }
        } else {
            return;
        }

        include $classFile;

        if (YIIS_DEBUG && !class_exists($className, false) && !interface_exists($className, false) && !trait_exists($className, false)) {
            throw new UnknownClassException("Unable to find '$className' in file: $classFile. Namespace missing?");
        }
    }
}

