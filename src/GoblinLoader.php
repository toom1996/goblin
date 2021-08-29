<?php

namespace toom1996;

use toom1996\base\InvalidConfigException;
use toom1996\http\Goblin;
use toom1996\http\UrlManager;
use toom1996\log\FileTarget;

/**
 * This constant defines the framework installation directory.
 */
defined('GOBLIN_PATH') or define('GOBLIN_PATH', __DIR__);

/**
 * Register autoload function.
 */
spl_autoload_register(['toom1996\GoblinLoader', 'autoload'], true, true);

/**
 * Class GoblinLoader
 *
 * @author TOOM1996
 * 
 */
class GoblinLoader
{
    /**
     * Application config.
     * @var array
     */
    public $config;

    /**
     * GoblinLoader constructor.
     *
     * @param  array  $config
     */
    public function __construct(&$config = [])
    {
        // Set alias and init config.
        $this->config = $config;
        Goblin::setAlias('@goblin', __DIR__);
        $this->initInitialize();
    }

    /**
     * Init config for Application
     *
     * @throws InvalidConfigException
     */
    public function initInitialize()
    {
        // Set aliases.
        if (isset($this->config['aliases']) && is_array($this->config['aliases'])) {
            foreach ($this->config['aliases'] as $alias => $path) {
                Goblin::setAlias($alias, $path);
            }
        }

//        $config = array_merge(Goblin::$config['components'][$id], (array)$params);
//        foreach ($config as $name => $value) {
//            if (property_exists($this, $name)) {
//                $this->{$name} = $value;
//            }
//        }
        // merge core components with custom components.
        foreach ($this->httpBaseComponents() as $id => $component) {
            if (!isset($this->config['components'][$id])) {
                $this->config['components'][$id] = $component;
            }

            if (!isset($this->config['components'][$id]['class'])) {
                $this->config['components'][$id]['class'] = $component['class'];
            }
        }
        $this->config['components']['urlManager']['adapter'] = UrlManager::loadRoute($this->config);
    }

    /**
     * returns goblin application.
     * @param $app
     *
     * @return \toom1996\http\Goblin
     */
    public function load($app, $request = null, $response = null)
    {
        return new $app($this->config, $request, $response);
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