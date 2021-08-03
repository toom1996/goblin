<?php

use toom1996\base\UnknownClassException;
use toom1996\web\Request;
use toom1996\web\UrlManager;
use toom1996\BaseYiiS;

/**
 * Class Application
 *
 * @author: TOOM <1023150697@qq.com>
 * @property-read \toom1996\web\Request $request
 * @property-read \toom1996\web\ErrorHandler $errorHandler
 * @property-read \toom1996\web\Response $response
 */
class YiiS extends BaseYiiS
{
    /**
     * The requested route
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
    protected static $config;


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
     * Run application
     *
     * @param  \Swoole\Http\Request   $request
     * @param  \Swoole\Http\Response  $response
     *
     * @return mixed
     */
    public function run($request, \Swoole\Http\Response $response)
    {
        try {
            ob_start();
            $this->bootstrap();
            $response->detach();
            $this->getResponse([
                'fd' => $response->fd
            ]);
            var_dump(memory_get_usage());
            return $this->handleRequest($this->getRequest($request))
                ->send();
        } catch (Throwable $e) {
            return $this->end($e->getMessage());
//            $this->getErrorHandler()->handleException($e);
        }
    }

    /**
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
     * @return \toom1996\web\ErrorHandler
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
     * @return \toom1996\web\Response
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
     * Handles request.
     *
     * @param \toom1996\web\Request $request
     *
     * @return \toom1996\web\Response
     */
    public function handleRequest($request)
    {

        try {
            list($route, $params) = $request->resolve();
        } catch (\toom1996\base\NotFoundHttpException $e) {
            // TODO 跳转到404页面
            YiiS::$app->response->setStatusCode(403);
            var_dump($e->getMessage());

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
        echo 'sdfsdfsdfsd';
//        try {
//            Yii::debug("Route requested: '$route'", __METHOD__);
            $this->requestedRoute = $route;
            $result = $this->runAction($route);
//            var_dump($result);
//            if ($result instanceof Response) {
//                return $result;
//            }
//
            $response = $this->getResponse();
//            if ($result !== null) {
            $response->content = ob_get_clean();
//            }
//
//            return $response;
//        } catch (InvalidRouteException $e) {
//            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'), $e->getCode(), $e);
//        }
        return $response;
    }

    public function init()
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

    public function createObject($type)
    {
        if (is_string($type)) {
            return self::$app->component($type);
        }
    }

    /**
     * Returns default YIIS core component.
     * @return array
     */
   public function coreComponents()
   {
       return array_merge(parent::coreComponents(), [
           'request' => ['class' => 'toom1996\web\Request'],
           'response' => ['class' => 'toom1996\web\Response'],
           'errorHandler' => ['class' => 'toom1996\web\ErrorHandler'],
       ]);
   }


   public function end($content)
   {
       $response = $this->getResponse();
       $response->content = $content;
       return $response->send();
   }
}
