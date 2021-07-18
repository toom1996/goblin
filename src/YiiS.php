<?php

namespace toom1996;


use toom1996\base\Module;
use toom1996\web\Request;
use toom1996\web\UrlManager;

/**
 * Class Application
 *
 * @author: TOOM <1023150697@qq.com>
 * @property-read Request $request
 * @property-read ErrorHandler $errorHandler
 */
class YiiS extends BaseYiiS
{
    /**
     * the requested route
     * @var string
     */
    public $requestedRoute;

    /**
     * Application app
     * @var YiiS
     */
    public static $app;

    /**
     * Application config
     * @var array
     */
    public static $config;

    /**
     * Application component
     * @var
     */
    protected $component;

    /**
     * YiiS constructor.
     *
     * @param  array  $config
     */
    public function __construct($config = [])
    {
        self::$config = $config;
    }

    /**
     * 
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->component($name);
    }

    /**
     *  运行app
     *  Run application
     *
     * @param  \Swoole\Http\Request   $request
     * @param  \Swoole\Http\Response  $response
     *
     * @return mixed
     */
    public function run($request, \Swoole\Http\Response &$response)
    {
        try {
            $this->bootstrap();
            return $this->handleRequest($this->getRequest($request))
                ->send($response);
        } catch (Throwable $e) {
            $this->getErrorHandler()->handleException($e);
        }
    }

    /**
     * 初始化并引到部分组件
     * Initializes and executes bootstrap components.
     */
    public function bootstrap()
    {
        $this->init();
    }

    /**
     * Returns the request component.
     *
     * @param $request
     *
     * @return Request
     */
    public function getRequest($request = null)
    {
        return $this->component('request', $request);
    }

    /**
     * Returns the errorHandler component.
     *
     * @return ErrorHandler
     */
    public function getErrorHandler()
    {
        return $this->component('errorHandler');
    }

    /**
     * Returns the request component.
     *
     * @param  null  $response
     *
     * @return Response
     */
    public function getResponse($response = null)
    {
        return $this->component('response', $response);
    }

    /**
     * Returens the urlManager component.
     * @return UrlManager
     */
    public function getUrlManager()
    {
        return $this->component('urlManager');
    }


    /**
     * Handles the specified request.
     *
     * @param Request $request
     *
     * @return \Response
     */
    public function handleRequest($request)
    {

        try {
            list($route, $params) = $request->resolve();
        } catch (UrlNormalizerRedirectException $e) {
//                $url = $e->url;
//                if (is_array($url)) {
//                    if (isset($url[0])) {
//                        // ensure the route is absolute
//                        $url[0] = '/' . ltrim($url[0], '/');
//                    }
//                    $url += $request->getQueryParams();
//                }
//
//                return $this->getResponse()->redirect(Url::to($url, $e->scheme), $e->statusCode);
        }
//        try {
//            Yii::debug("Route requested: '$route'", __METHOD__);
            $this->requestedRoute = $route;
            $result = $this->runAction($route, $params);
//            if ($result instanceof Response) {
//                return $result;
//            }
//
//            $response = $this->getResponse();
//            if ($result !== null) {
//                $response->data = $result;
//            }
//
//            return $response;
//        } catch (InvalidRouteException $e) {
//            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'), $e->getCode(), $e);
//        }
        return $this->getResponse();
    }

    public function init()
    {
        self::$app = $this;
    }


    public function component($id, $value = null)
    {
        echo "instance component {$id}" . PHP_EOL;
        if (isset($this->component[$id])) {
            echo "is instance component {$id}" . PHP_EOL;
            return $this->component[$id];
        }

        if (isset(self::$config['components'][$id])) {
            echo "create {$id} component" . PHP_EOL;
            $className = self::$config['components'][$id]['class'];
            return $this->component[$id] = new $className($id, $value);
        }else{
            echo "can't find {$id} component";
//            throw new Error("can't find {$id} component");
        }

    }

    public function createObject($type)
    {
        if (is_string($type)) {
            return self::$app->component($type);
        }
    }

    /**
     * Returns the configuration of core application components.
     * @return array
     */
   public function coreComponents()
   {
       return array_merge(parent::coreComponents(), [
           'request' => ['class' => 'yii\web\Request'],
           'response' => ['class' => 'yii\web\Response'],
           'session' => ['class' => 'yii\web\Session'],
           'user' => ['class' => 'yii\web\User'],
           'errorHandler' => ['class' => 'yii\web\ErrorHandler'],
       ]);
   }
}
