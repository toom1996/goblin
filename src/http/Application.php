<?php
namespace toom1996\http;

use Swoole\Coroutine;
use toom1996\base\AnnotationScanner;
use toom1996\base\Exception;
use toom1996\base\Module;
use toom1996\base\UnknownClassException;
use toom1996\helpers\BaseFileHelper;
use toom1996\http\UrlManager;


/**
 * This constant defines the framework installation directory.
 */
defined('APP_PATH') or define('APP_PATH', __DIR__);

/**
 * Register autoload function.
 */
spl_autoload_register(['toom1996\http\Application', 'autoload'], true, true);
/**
 * Base http application.
 * Defined all public variables.
 *
 * @author: TOOM <1023150697@qq.com>
 */
class Application
{
    /**
     * Application config.
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
    public function __construct(&$config = [])
    {
        // Set alias and init config.
        $this->config = $config;
        Goblin::setAlias('@app', $this->config['basePath']);
        Goblin::setAlias('@goblin', dirname(__DIR__));
        Goblin::setAlias('@controllers', $this->config['controllersPath']);
        $this->initConfig();
    }

    /**
     * Init config for Application
     * @throws \ReflectionException
     * @throws \toom1996\base\Exception
     */
    public function initConfig()
    {
        if (isset($this->config['aliases']) && is_array($this->config['aliases'])) {
            foreach ($this->config['aliases'] as $alias => $path) {
                Goblin::setAlias($alias, $path);
            }
        }
        $this->config['components']['urlManager']['adapter'] = UrlManager::loadRoute($this->config);
    }

    /**
     *
     * @param $app
     *
     * @return \toom1996\http\Goblin
     */
    public function load($app)
    {
        return new $app($this->config);
    }

    public static function autoload(string $className)
    {
        if (strpos($className, '\\') !== false) {
            $classFile = Goblin::getAlias('@' . str_replace('\\', '/', $className) . '.php', false);
            if ($classFile === false || !is_file($classFile)) {
                return;
            }
        } else {
            return;
        }

        require $classFile;

//        if (YIIS_DEBUG && !class_exists($className, false) && !interface_exists($className, false) && !trait_exists($className, false)) {
//            throw new UnknownClassException("Unable to find '$className' in file: $classFile. Namespace missing?");
//        }
    }
}

