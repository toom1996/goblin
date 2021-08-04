<?php
namespace toom1996;

use Swoole\Coroutine;
use toom1996\base\AnnotationScanner;
use toom1996\base\Exception;
use toom1996\base\Module;
use toom1996\base\UnknownClassException;
use toom1996\helpers\BaseFileHelper;
use toom1996\web\UrlManager;


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

    /**
     * Application constructor.
     *
     * @param  array  $config
     *
     * @throws \ReflectionException
     * @throws \toom1996\base\Exception
     */
    public function __construct($config = [])
    {
        \YiiS::setAlias('@app', $config['basePath']);
        $this->config = $config;
        $this->initConfig();
    }

    /**
     * Init config for Application
     * @throws \ReflectionException
     * @throws \toom1996\base\Exception
     */
    public function initConfig()
    {
        $this->config['scanner']['arguments'] = $this->scanRouter();

        $this->config['components']['urlManager']['route'] = UrlManager::buildRouteTree($this->config);
        var_dump($this->config);
    }

    /**
     * Scan all route
     * @return array
     * @throws \ReflectionException
     * @throws \toom1996\base\Exception
     */
    private function scanRouter(): array
    {
        return (new AnnotationScanner($this->config))->scan();
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
        var_dump(memory_get_usage());
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

//        if (YIIS_DEBUG && !class_exists($className, false) && !interface_exists($className, false) && !trait_exists($className, false)) {
//            throw new UnknownClassException("Unable to find '$className' in file: $classFile. Namespace missing?");
//        }
    }
}

