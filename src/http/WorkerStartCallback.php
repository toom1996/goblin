<?php


namespace toom1996\http;


use toom1996\base\Exception;
use toom1996\base\Stdout;
use toom1996\di\Container;
use toom1996\helpers\ConsoleHelper;

class WorkerStartCallback
{
    
    public static function onWorkerStart($server, int $workerId)
    {
        if ($server->taskworker) {
           $workerAlias = "TaskWorker#{$workerId}";
        } else {
            $workerAlias = "Worker#{$workerId}";
        }

        
        Stdout::info($workerAlias);
        swoole_set_process_name($workerAlias);
        register_shutdown_function(function() {
            Stdout::error(error_get_last());
        });
        
        Eazy::$config = require APP_PATH . '/config/config.php';
        spl_autoload_register(function($className) {
            if (strpos($className, '\\') !== false) {
                $classFile = Eazy::getAlias('@' . str_replace('\\', '/', $className) . '.php', false);
                if ($classFile === false || !is_file($classFile)) {
                    return;
                }
            } else {
                return;
            }

            require $classFile;
        }, true, true);
        self::initConfigure();
    }

    /**
     * Initialize configure.
     * Set aliases and merge core components.
     */
    public static function initConfigure()
    {
        // Set aliases.
        if (isset(Eazy::$config['aliases']) && is_array(Eazy::$config['aliases'])) {
            foreach (Eazy::$config['aliases'] as $alias => $path) {
                Eazy::setAlias($alias, $path);
            }
        }

        // merge core components with custom components.
        foreach ([
            'request' => ['class' => 'toom1996\http\Request'],
            'response' => ['class' => 'toom1996\http\Response'],
            'errorHandler' => ['class' => 'toom1996\http\ErrorHandler'],
            'urlManager' => ['class' => 'toom1996\http\UrlManager'],
            'view' => ['class' => 'toom1996\http\View'],
            'assetManager' => ['class' => 'toom1996\http\AssetManager'],
            'log' => ['class' => 'toom1996\log\LogDispatcher'],
        ] as $id => $component) {
            if (!isset(Eazy::$config['components'][$id])) {
                Eazy::$config['components'][$id] = $component;
            }

            if (!isset(Eazy::$config['components'][$id]['class'])) {
                Eazy::$config['components'][$id]['class'] = $component['class'];
            }
        }
        Eazy::$config['components']['urlManager']['adapter'] = UrlManager::loadRoute(Eazy::$config);

        foreach (Eazy::$config['bootstrap'] as $component) {
            $def = Eazy::$config['components'][$component];
            Eazy::$config['components'][$component] = Eazy::createObject($def);
        }
    }
}