<?php

namespace toom1996\http;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use toom1996\base\InvalidConfigException;
use function FastRoute\simpleDispatcher;

/**
 * Class UrlManager
 *
 * @author: TOOM1996
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
     *
     *
     * @return array
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     * @throws \ReflectionException
     * @throws InvalidConfigException
     */
    public function parseRequest(): array
    {
        $match = $this->matchRoute();
        switch ($match[0]) {
            case Dispatcher::NOT_FOUND:
                throw new NotFoundHttpException("Page Not Found.");
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new MethodNotAllowedHttpException("Method Not Allowed.");
                break;
        }
        // handler, param
        return [$match[1], $match[2]];
    }


    /**
     * Trim url slashes.
     * `/xxx/xxx////1///xxx` will be trim to `/xxx/xxx/1/xxx`
     *
     * @param $url
     *
     * @return string
     */
    private static function trimSlashes($url): string
    {
        if ($url !== '/') {
            $url = rtrim($url, '/');
        }

        $url = preg_replace('#/+#', '/', $url);
        if ($url === '') {
            return '/';
        }

        return $url;
    }

    /**
     *
     *
     * @return array
     * @throws \ReflectionException
     * @throws InvalidConfigException
     */
    protected function matchRoute()
    {
        $request = Goblin::$app->getRequest();
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
        $webRoute = $config['route'];

        return simpleDispatcher(function (RouteCollector $controller) use (
            $webRoute
        ) {
            foreach ($webRoute as $prefix => $rules) {
                if (count($rules) == count($rules, COUNT_RECURSIVE)) {
                    list($method, $route, $handler) = self::parseRule($rules);
                    $controller->addRoute($method, $route, $handler);
                } else {
                    if (is_int($prefix)) {
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
            }
        });
    }

    /**
     * Parse route and add to handlerMap.
     *
     * @param $rule
     *
     * @return array
     * @throws \ReflectionException
     */
    private static function parseRule($rule)
    {
        list($method, $route, $handler) = [$rule[0], $rule[1], $rule[2]];
        if (strpos($handler, '@') === 0) {
            Goblin::createController($handler);
        }
        return [$method, $route, $handler];
    }


}