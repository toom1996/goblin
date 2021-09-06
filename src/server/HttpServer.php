<?php


namespace toom1996\server;


use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;
use toom1996\Application;
use toom1996\base\BaseServer;
use toom1996\base\InvalidConfigException;
use toom1996\base\UnknownClassException;
use toom1996\http\Eazy;
use toom1996\http\Goblin;
use toom1996\http\UrlManager;

/**
 * Class HttpServer
 *
 * @author: TOOM1996
 */
class HttpServer extends BaseServer
{
    use ServerHttpTrait;

    const HTTP_EVENT = [
        'start',
        'request',
        'workerStart',
    ];

    public function init()
    {
        $this->server = new \Swoole\Http\Server($this->host, $this->port);
        foreach (self::HTTP_EVENT as $event) {
            $this->server->on($event, [$this, $event]);
        }
        parent::init();
    }

    /**
     *
     *
     * @param  Request   $request
     * @param  Response  $response
     *
     * @throws \ReflectionException
     * @throws InvalidConfigException
     * @throws UnknownClassException
     */
    public function request(Request $request, Response $response)
    {
//        return $response->end('123');
        (new Eazy($this->config, $request, $response))->run();
    }

    public function workerStart()
    {
        register_shutdown_function(function() {
            var_dump(error_get_last());
        });

        $this->config = require APP_PATH . '/config/config.php';
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
        // Set aliases.
        if (isset($this->config['aliases']) && is_array($this->config['aliases'])) {
            foreach ($this->config['aliases'] as $alias => $path) {
                Eazy::setAlias($alias, $path);
            }
        }

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

    public function run()
    {
        $this->server->start();
    }
}