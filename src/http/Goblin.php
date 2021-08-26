<?php


namespace toom1996\http;

use Swoole\Http\Request;
use Swoole\Http\Response;
use toom1996\base\InvalidConfigException;
use toom1996\base\UnknownClassException;
use toom1996\log\Target;

/**
 * Class Goblin
 *
 * @property-read Request  $request
 * @property-read ErrorHandler $errorHandler
 * @property-read Response $response
 * @property-read Target $log
 * @author: TOOM1996
 */
class Goblin extends BaseGoblin
{
    /**
     * [[网页编码格式。也许应该把他放到response里。]]
     * @var string
     */
    public $charset = 'UTF-8';

    /**
     * Goblin framework instance.
     * @var Goblin
     */
    public static $app;

    /**
     * Goblin instance config.
     * @var array
     */
    public static $config;

    /**
     * Goblin constructor.
     *
     * @param  array  $config
     * @param         $request
     * @param         $response
     *
     * @throws InvalidConfigException
     * @throws \ReflectionException
     */
    public function __construct(&$config = [], $request = null, $response = null)
    {
        self::$config = $config;
        $this->bootstrap();
        $this->getResponse($response);
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        echo 'goblin destruct';
    }

    /**
     * Runs goblin application.
     *
     * @param  \Swoole\Http\Request   $request   Swoole request object.
     * @param  \Swoole\Http\Response  $response  Swoole response object.
     *
     * @return bool
     * @throws \ReflectionException
     * @throws InvalidConfigException
     * @throws UnknownClassException
     */
    public function run(Request $request, Response $response)
    {
        try {
            self::$app->getLog()->messages = '21123123';
            $this->handleRequest($this->getRequest($request))
                ->send();
        }catch (\Swoole\ExitException $e){
            $this->getResponse()->content = $e->getStatus();
        }catch (\Throwable $e) {
            $this->getErrorHandler()->handleException($e);
        } finally {
            $this->getResponse()->send();
        }
        self::$app = null;
    }

    /**
     * Initializes and executes bootstrap components.
     */
    public function bootstrap()
    {
        // merge core components with custom components
        foreach ($this->coreComponents() as $id => $component) {
            if (!isset(self::$config['components'][$id])) {
                self::$config['components'][$id] = $component;
            } elseif (is_array(self::$config['components'][$id]) && !isset(self::$config['components'][$id]['class'])) {
                self::$config['components'][$id]['class'] = $component['class'];
            }
        }
        
        self::$app = &$this;
    }

    /**
     *
     * @param  null  $request
     *
     * @return \toom1996\http\Request
     * @throws \ReflectionException
     * @throws InvalidConfigException
     */
    public function getRequest($request = null)
    {
        if (!$this->has('request')) {
            $this->set('request', $request);
        }

        return $this->get('request');
    }

    /**
     *
     * @return View
     * @throws \ReflectionException
     * @throws InvalidConfigException
     */
    public function getView()
    {
        return $this->get('view');
    }

    /**
     *
     * @return ErrorHandler
     * @throws \ReflectionException
     * @throws InvalidConfigException
     */
    public function getErrorHandler()
    {
        return $this->get('errorHandler');
    }

    /**
     *
     * @param  null  $response
     *
     * @return \toom1996\http\Response
     * @throws \ReflectionException
     * @throws InvalidConfigException
     */
    public function getResponse($response = null)
    {
        if (!$this->has('response')) {
            $this->set('response', $response);
        }

        return $this->get('response');
    }

    /**
     *
     * @return \toom1996\http\UrlManager
     * @throws \ReflectionException
     * @throws InvalidConfigException
     */
    public function getUrlManager()
    {
        return $this->get('urlManager');
    }

    /**
     *
     *
     * @return mixed
     * @throws InvalidConfigException
     * @throws \ReflectionException
     */
    public function getAssetManager()
    {
        return $this->get('assetManager');
    }

    /**
     *
     *
     * @return Target
     * @throws InvalidConfigException
     * @throws \ReflectionException
     */
    public function getLog()
    {
        return $this->get('log');
    }

    /**
     *
     *
     * @param $request \toom1996\http\Request
     *
     * @return \toom1996\http\Response
     * @throws InvalidConfigException
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     * @throws UnknownClassException
     * @throws \ReflectionException
     */
    public function handleRequest($request)
    {
        [$handler, $params] = $request->resolve();
        $result = $this->runAction($handler);
        $response = $this->getResponse();
        if ($result !== null) {
            $response->content = $result;
        }

        return $response;
    }

    /**
     * Returns default goblin components.
     * @return array
     */
    public function coreComponents()
    {
        return array_merge(parent::coreComponents(), [
            'request' => ['class' => 'toom1996\http\Request'],
            'response' => ['class' => 'toom1996\http\Response'],
            'errorHandler' => ['class' => 'toom1996\http\ErrorHandler'],
            'urlManager' => ['class' => 'toom1996\http\UrlManager'],
            'view' => ['class' => 'toom1996\http\View'],
            'assetManager' => ['class' => 'toom1996\http\AssetManager'],
        ]);
    }

    /**
     *
     * @param  int  $code
     *
     * @throws \ReflectionException
     * @throws InvalidConfigException
     */
    public function end($code = 200)
    {
        $response = $this->getResponse();
        $response->setStatusCode($code);
        $response->send();
    }

    /**
     * Print to browser.
     * @param $output
     *
     * @throws \ReflectionException
     * @throws InvalidConfigException
     */
    public static function dump($output)
    {
        self::$app->getResponse()->setContent(print_r($output, true));
    }
}