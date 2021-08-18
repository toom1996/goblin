<?php

namespace toom1996\http;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
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

        return preg_replace('#/+#', '/', $url);
    }


    /**
     * Match route.
     *
     * @return array
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
                if (count($rules) == count($rules, 1)) {
                    list($method, $route, $handler) = self::parseRule($rules);
                    $controller->addRoute($method, $route, $handler);
                } else {
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
     * Parse rules and add to handlerMap.
     *
     * @param $rule
     *
     * @return array
     */
    private static function parseRule($rule)
    {
        list($method, $route, $handler) = [$rule[0], $rule[1], $rule[2]];
        if (strpos($handler, '@') === 0) {
            // If route is `@controllers/site/index`, will be convert @controller to BathPath
            $handlerAlias = Goblin::getAlias($handler);
            $ex = explode('/', $handlerAlias);

            // Find controller and action.
            list($controller, $action) = array_slice($ex, -2, 2);

            // will be convert to `$bathPath/SiteController/index`
            if (strpos($controller, 'Controller') === false) {
                $controller = ucfirst($controller).'Controller';
            }

            // will be convert to `$bathPath/SiteController/actionIndex`
            if (strpos($action, 'action') === false) {
                $action = 'action'.ucfirst($action);
            }
            $handlerFile = implode('/',
                array_merge(array_slice($ex, 0, count($ex) - 2),
                    [$controller . '.php']));
            $className = '\\' . Goblin::getNamespace($handlerFile) . '\\' . basename(str_replace('.php', '', $handlerFile));

            Goblin::setHandlerMap($handler, [
                'class' => $className,
                'action' => $action
            ]);
        }
        return [$method, $route, $handler];
    }


}