<?php


namespace toom1996\server\http;


use Swoole\Coroutine;
use Swoole\Database\RedisConfig;
use Swoole\Database\RedisPool;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server as swooleServer;
use Swoole\Runtime;
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
 * @since 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class Server extends BaseServer
{
    use HttpTrait;

    const HTTP_EVENT = [
        'start',
        'request',
        'workerStart',
    ];

    public function init()
    {
        $this->server = new swooleServer($this->host, $this->port);
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
//        $pool = new RedisPool((new RedisConfig())
//            ->withHost('172.17.0.3')
//            ->withPort(6379)
//            ->withAuth('')
//            ->withDbIndex(0)
//            ->withTimeout(1), 100
//        );
//         $n = 10240;

//        $s = microtime(true);
//        for ($n = 10240; $n--;) {
//            Coroutine::create(function () use ( $n, $pool) {
                (new Eazy($this->config, $request, $response))->run();
//                $redis = $pool->get();
//                $result = $redis->set('foo' . $n, 'bar');
//                if (!$result) {
//                    throw new RuntimeException('Set failed');
//                }
//                $result = $redis->get('foo' . $n);
//                if ($result !== 'bar') {
//                    throw new RuntimeException('Get failed');
//                }
//                $pool->put($redis);
//            });
//        }

//        $s = microtime(true) - $s;
//        echo 'Use ' . $s . 's for ' . ($n * 2) . ' queries' . PHP_EOL;
//        (new Eazy($this->config, $request, $response))->run();
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

        $this->initConfigure();
    }

    /**
     * Initialize configure.
     * Set aliases and merge core components.
     */
    public function initConfigure()
    {
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

        foreach ($this->config['bootstrap'] as $component) {
            $def = $this->config['components'][$component];
            $this->config['components'][$component] = Eazy::createObject($def);
        }
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

    /**
     * Run swoole http server.
     */
    public function run()
    {
        $this->server->start();
    }
}