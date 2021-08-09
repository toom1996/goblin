<?php

namespace toom1996\web;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use toom1996\base\NotFoundHttpException;
use toom1996\http\BaseUrlManager;
use toom1996\http\MethodNotAllowedHttpException;
use function FastRoute\simpleDispatcher;

/**
 * Class UrlManager
 *
 * @author: TOOM <1023150697@qq.com>
 */
class UrlManager extends BaseUrlManager
{

    /**
     * @var Dispatcher
     */
    protected $adapter;

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

        return $this->adapter->dispatch($httpMethod, $uri);
    }


    /**
     * Load config route.
     *
     * @param $config
     *
     * @return Dispatcher
     */
    public static function loadRoute($config)
    {
        $webRoute = self::getRoute($config);
        return simpleDispatcher(function (RouteCollector $controller) use ($webRoute) {
            foreach ($webRoute as $prefix => $rules) {
                if (count($rules) == count($rules, 1)) {
                    list($method, $route, $handler) = self::parseRule($rules);
                    $controller->addRoute($method, $route, $handler);
                }else{
                    $controller->addGroup($prefix, function (RouteCollector $controller) use ($rules) {
                        foreach ($rules as $rulesChild) {
                            list($method, $route, $handler) = self::parseRule($rulesChild);
                            $controller->addRoute($method, $route, $handler);
                        }
                    });
                }
            }
        });
    }

    /**
     * Parse rules.
     * @param $rule
     *
     * @return array
     */
    private static function parseRule($rule)
    {
        list($method, $route, $handler) = [$rule[0], $rule[1], $rule[2]];
        if (strpos($handler, '@') === 0) {
            $handler = \YiiS::getAlias($handler);
            var_dump($handler);
        }
        return [$method, $route, $rule[2]];
    }
}