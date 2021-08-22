<?php

namespace toom1996;

use toom1996\http\Goblin;
use toom1996\http\UrlManager;

/**
 * This constant defines the framework installation directory.
 */
defined('GOBLIN_PATH') or define('GOBLIN_PATH', __DIR__);

var_dump(GOBLIN_PATH);
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
        Goblin::setAlias('@app', $this->config['basePath']);
        Goblin::setAlias('@goblin', __DIR__);
        Goblin::setAlias('@controllers', $this->config['controllersPath']);
        $this->initInitialize();
    }

    /**
     * Init config for Application
     */
    public function initInitialize()
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

    /**
     *
     *
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

    public function createServer()
    {

    }
}