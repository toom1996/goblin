<?php


namespace toom1996\http;

use Swoole\Http\Request;
use Swoole\Http\Response;

/**
 * Class Goblin
 *
 * @property-read \toom1996\web\Request $request
 * @property-read \toom1996\http\ErrorHandler $errorHandler
 * @property-read \toom1996\web\Response $response
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
     * 框架实例。
     * Goblin framework instance.
     * @var Goblin
     */
    public static $app;

    /**
     * 框架实例配置。
     * Goblin instance config.
     * @var array
     */
    public static $config;

    /**
     * Goblin constructor.
     *
     * @param  array  $config
     */
    public function __construct($config = [])
    {
        self::$config = $config;
    }

    /**
     * 运行Goblin应用。
     * Runs goblin application.
     * 这是应用的主入口。
     * This is main entrance of an application.
     *
     * @param \Swoole\Http\Request $request Swoole request object.
     * @param  \Swoole\Http\Response  $response Swoole response object.
     *
     * @return bool
     * @throws \ReflectionException
     * @throws \toom1996\base\InvalidConfigException
     */
    public function run(Request $request, Response $response)
    {
        try {
            $this->bootstrap();
            // Detach swoole response
            $response->detach();
            // See https://wiki.swoole.com/#/http_server?id=create-1
            $this->getResponse([
                'fd' => $response->fd
            ]);
            return $this->handleRequest($this->getRequest($request))
                ->send();
        }catch (\Swoole\ExitException $e){
            $this->getResponse()->content = 'exit';
        }catch (\Throwable $e) {
            $this->getErrorHandler()->handleException($e);
        } finally {
            return $this->getResponse()->send();
        }
    }

    /**
     * 初始化引导组件。
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
        self::$app = $this;
    }

    /**
     *
     * @param  null  $request
     *
     * @return Request
     * @throws \ReflectionException
     * @throws \toom1996\base\InvalidConfigException
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
     * @return \toom1996\http\View
     * @throws \ReflectionException
     * @throws \toom1996\base\InvalidConfigException
     */
    public function getView()
    {
        return $this->get('view');
    }

    /**
     *
     * @return \toom1996\http\ErrorHandler
     * @throws \ReflectionException
     * @throws \toom1996\base\InvalidConfigException
     */
    public function getErrorHandler()
    {
        return $this->get('errorHandler');
    }

    /**
     *
     * @param  null  $response
     *
     * @return \toom1996\web\Response
     * @throws \ReflectionException
     * @throws \toom1996\base\InvalidConfigException
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
     * @throws \toom1996\base\InvalidConfigException
     */
    public function getUrlManager()
    {
        return $this->get('urlManager');
    }

    /**
     *
     * @param $request Request
     *
     * @return mixed
     * @throws \ReflectionException
     * @throws \toom1996\base\InvalidConfigException
     * @throws \toom1996\base\UnknownClassException
     * @throws \toom1996\base\InvalidConfigException
     */
    public function handleRequest($request)
    {
        try {
            list($handler, $params) = $request->resolve();
        } catch (\toom1996\base\NotFoundHttpException $e) {
            $handler = YiiS::$app->getErrorHandler()->errorAction;
        }
        $result = $this->runAction($handler);
        if ($result === false) {

        }

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
        ]);
    }


    /**
     *
     * @param  int  $code
     *
     * @throws \ReflectionException
     * @throws \toom1996\base\InvalidConfigException
     */
    public function end($code = 200)
    {
        $response = $this->getResponse();
        $response->setStatusCode($code);
        $response->send();
    }
}