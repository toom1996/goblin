<?php

namespace toom1996\web;

use FastRoute\Dispatcher;
use FastRoute\Route;
use FastRoute\RouteCollector;
use http\Exception\BadMethodCallException;
use Psr\Container\ContainerInterface;
use toom1996\base\Component;
use toom1996\base\Exception;
use toom1996\base\NotFoundHttpException;
use toom1996\http\BaseUrlManager;
use toom1996\http\MethodNotAllowedHttpException;
use toom1996\helpers\BaseArrayHelper;
use toom1996\helpers\BaseFileHelper;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\UrlRule;
use function FastRoute\simpleDispatcher;

/**
 * Class UrlManager
 *
 * @author: TOOM <1023150697@qq.com>
 */
class UrlManager extends BaseUrlManager
{

    /**
     * Init UrlManager
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Parse request.
     * It will return an array of return values consisting of a method and a matching data.
     *
     *
     * @return array
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function parseRequest(): array
    {
        $match = $this->matchRoute();
        
        switch ($match[0]) {
            case Dispatcher::NOT_FOUND:
                throw new NotFoundHttpException("404 Page not found.");
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new MethodNotAllowedHttpException("405 Method Not Allowed");
                break;
        }
        return [$match[1], $match[2]];
    }


    /**
     * Trim url slashes.
     * `/xxx/xxx////1///xxx` will be trim to `/xxx/xxx/1/xxx`
     * @param $url
     *
     * @return string
     */
    private static function trimSlashes($url): string
    {
        if ($url !== '/') {
            $url = rtrim($url, '/');
        }
        return preg_replace('#/+#', '/', $url);
    }



    /**
     *
     *
     *
     * @return array
     */
    protected function matchRoute()
    {
        $request = \YiiS::$app->getRequest();
        $httpMethod = $request->getMethod();
        $uri = self::trimSlashes($request->getUrl());
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $this->adapter->dispatch($httpMethod, $uri);

        return $routeInfo;
    }


    /**
     *
     *
     * @param $route
     *
     * @return Dispatcher
     */
    public static function loadRoute($config)
    {
        $webConfig = self::getRoute($config);
        $dispatcher = simpleDispatcher(function (RouteCollector $controller) use ($webConfig) {
            foreach ($webConfig as $item) {
                list($method, $route, $handler) = [$item[0], $item[1], $item[2]];
                $controller->addRoute($method, $route, $handler);
            }
            /**
            //             * 通过 addRoute() 添加路由
            //             * $method 必须是大写，可以写成数组形式
            //             * $routePattern /开头, 可以用正则修饰
            //             * $handler
            //             */
//            $controller->addRoute('GET', '/fast-route/demo1.php/{name:\w+}', 'say_handler');
//            // 分组
//            $r->addGroup('/admin', function (RouteCollector $r) {
//                // {id} must be a number (\d+)
//                $r->addRoute('GET', '/user/{id:\d+}.html', 'get_user_handler');
//                // The /{title} suffix is optional
//                $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
//                $r->addRoute('GET', '/users', 'get_all_users_handler');
//            });
        });
        return $dispatcher;
    }
}