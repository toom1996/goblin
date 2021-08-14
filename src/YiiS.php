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
 * @property-read \toom1996\http\ErrorHandler $errorHandler
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
    public static $config;


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
     * @param $request
     * @param  \Swoole\Http\Response  $response
     *
     * @return bool
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function run($request, \Swoole\Http\Response $response)
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
            $this->getResponse()->content = $e->getStatus();
        }catch (\Throwable $e) {
            $this->getErrorHandler()->handleException($e);
        } finally {
            return $this->getResponse()->send();
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
     * @return mixed
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
            YiiS::$app->getErrorHandler()->handleException($e);
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
            $result = $this->runAction($handler, $params);
            if ($result === false) {
                
            }
//
            $response = $this->getResponse();
            if ($result !== null) {
                $response->content = $result;
            }
//
//            return $response;
//        } catch (InvalidRouteException $e) {
//            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'), $e->getCode(), $e);
//        }
        return $response;
    }

    public function init()
    {
//        ob_start();
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
     * Returns default YIIS core component.
     * @return array
     */
   public function coreComponents()
   {
       return array_merge(parent::coreComponents(), [
           'request' => ['class' => 'toom1996\web\Request'],
           'response' => ['class' => 'toom1996\web\Response'],
           'errorHandler' => ['class' => 'toom1996\http\ErrorHandler'],
           'urlManager'   => ['class' => 'toom1996\http\UrlManager'],
           'view'         => ['class' => 'toom1996\http\View'],
       ]);
   }


    /**
     *
     * @param  int  $code
     *
     * @throws \ReflectionException
     * @throws \toom1996\base\InvalidConfigException
     */
   public function end($code = 0)
   {
       $response = $this->getResponse();
       $response->send();
       exit(0);
   }


}
