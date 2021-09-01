<?php


namespace toom1996;

use toom1996\base\InvalidConfigException;
use toom1996\http\Goblin;
use toom1996\http\UrlManager;
use toom1996\server\HttpServer;

/**
 * This constant defines the framework installation directory.
 */
defined('EAZY_PATH') or define('EAZY_PATH', __DIR__);

/**
 * Register autoload function.
 */
spl_autoload_register(['toom1996\Application', 'autoload'], true, true);

/**
 * Class Application
 *
 * @author: TOOM1996
 */
class Application
{
    /**
     * Application config.
     * @var array
     */
    public static $applicationConfig;

    /**
     * Application constructor.
     *
     * @param  array  $config
     */
    public function __construct(&$config = [])
    {
        // Set alias and init config.
        self::$applicationConfig = $config;
        Goblin::setAlias('@goblin', __DIR__);
        $this->initInitialize();
        $this->bootstrap();
    }


    /**
     * Init config for Application
     *
     */
    public function initInitialize()
    {
        // Set aliases.
        if (isset(self::$applicationConfig['aliases']) && is_array(self::$applicationConfig['aliases'])) {
            foreach (self::$applicationConfig['aliases'] as $alias => $path) {
                Goblin::setAlias($alias, $path);
            }
        }


        // merge core components with custom components.
        foreach ($this->httpBaseComponents() as $id => $component) {
            if (!isset(self::$applicationConfig['components'][$id])) {
                self::$applicationConfig['components'][$id] = $component;
            }

            if (!isset(self::$applicationConfig['components'][$id]['class'])) {
                self::$applicationConfig['components'][$id]['class'] = $component['class'];
            }
        }
        self::$applicationConfig['components']['urlManager']['adapter'] = UrlManager::loadRoute(self::$applicationConfig);
    }

    public function bootstrap()
    {
        foreach (Application::$applicationConfig['bootstrap'] as $component) {
            $def = self::$applicationConfig['components'][$component];
            unset($def['class']);
            self::$applicationConfig['components'][$component] = Goblin::createObject(self::$applicationConfig['components'][$component]['class'], [$def]);
        }
    }

    /**
     * Return swoole server.
     * @return HttpServer
     */
    public function createServer()
    {
        return new HttpServer(self::$applicationConfig['swoole']);
    }

    /**
     * Autoload function.
     * @param  string  $className
     */
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
    }

    public function httpBaseComponents()
    {
        return [
            'request' => ['class' => 'toom1996\http\Request'],
            'response' => ['class' => 'toom1996\http\Response'],
            'errorHandler' => ['class' => 'toom1996\http\ErrorHandler'],
            'urlManager' => ['class' => 'toom1996\http\UrlManager'],
            'view' => ['class' => 'toom1996\http\View'],
            'assetManager' => ['class' => 'toom1996\http\AssetManager'],
            'log' => ['class' => 'toom1996\log\LogDispatcher'],
        ];
    }
}