<?php

namespace toom1996\http;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use toom1996\base\Component;
use toom1996\base\InvalidConfigException;
use toom1996\base\UnknownClassException;
use toom1996\helpers\FileHelper;
use function FastRoute\simpleDispatcher;

/**
 * Class UrlManager
 *
 * @property array $controllerMap urlManager controller map.
 *
 * @author: TOOM1996
 * @since 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class UrlManager extends Component
{

    /**
     * @var array
     */
    public array $route;

    /**
     * @var
     */
    private $_adapter;

    /**
     * @var array 
     */
    private array $_controllerMap;

    /**
     * Init UrlManager
     */
    public function init()
    {
        $this->_adapter = $this->getAdapter();
        var_dump($this->_controllerMap);
    }

    /**
     *
     * @return array
     * @throws \ReflectionException
     * @throws \toom1996\base\InvalidConfigException
     * @throws \toom1996\http\MethodNotAllowedHttpException
     * @throws \toom1996\http\NotFoundHttpException
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
     * @return array
     * @throws \ReflectionException
     * @throws \toom1996\base\InvalidConfigException
     */
    protected function matchRoute()
    {
        $request = Eazy::$app->getRequest();
        $httpMethod = $request->getMethod();
        $uri = self::trimSlashes($request->getUrl());
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        return $this->_adapter->dispatch($httpMethod, $uri);
    }


    /**
     * Load config route.
     *
     * @param $config
     *
     * @return Dispatcher
     */
    private function getAdapter()
    {
        $webRoute = $this->route;
        return simpleDispatcher(function (RouteCollector $controller) use ($webRoute) {
            foreach ($webRoute as $prefix => $rules) {
                if (count($rules) == count($rules, COUNT_RECURSIVE)) {
                    [$method, $route, $handler] = $this->parseRule($rules);
                    $controller->addRoute($method, $route, $handler);
                } else {
                    if (is_int($prefix)) {
                        [$method, $route, $handler] = $this->parseRule($rules);
                        $controller->addRoute($method, $route, $handler);
                    }else{
                        $controller->addGroup($prefix, function (RouteCollector $controller) use ($rules) {
                            foreach ($rules as $rulesChild) {
                                [$method, $route, $handler] = $this->parseRule($rulesChild);
                                $controller->addRoute($method, $route, $handler);
                            }
                        });
                    }
                }
            }
        });
    }

    /**
     *
     * @param $rule
     *
     * @return array
     * @throws \ReflectionException
     */
    private function parseRule($rule)
    {
        [$method, $route, $handler] = [$rule[0], $rule[1], $rule[2]];
        if (strpos($handler, '@') !== 0) {
            $handler = '@controllers' . $handler;
        }
        $this->setControllerMap($handler);

        return [$method, $route, $handler];
    }

    /**
     * Set to controller map.
     * @param $handler
     *
     * @throws \ReflectionException
     * @throws \toom1996\base\InvalidConfigException
     * @throws \toom1996\base\UnknownClassException
     */
    public function setControllerMap($handler)
    {
        // If route is `@controllers/site/index`, will be convert @controller to BathPath
        $handlerAlias = Eazy::getAlias($handler);
        $ex = explode('/', $handlerAlias);

        // Find controller and action.
        [$controller, $action] = array_slice($ex, -2, 2);

        if (strpos($controller, 'Controller') === false) {
            $controller = ucfirst($controller).'Controller';
        }

        if (strpos($action, 'action') === false) {
            $action = 'action'.ucfirst($action);
        }

        $handlerFile = implode('/',
            array_merge(array_slice($ex, 0, count($ex) - 2),
                [$controller . '.php']));
        if (!file_exists($handlerFile)) {
            throw new UnknownClassException("{Unknown class {$handler}");
        }

        $classNamespace = FileHelper::getNamespace($handlerFile);
        $className = '\\' . $classNamespace . '\\' . basename(str_replace('.php', '', $handlerFile));

        $ref = new \ReflectionClass($className);
        if (!$ref->hasMethod($action)) {
            throw new InvalidConfigException("class {$className} does not have a method {$action}, please check your config.");
        }

        $this->_controllerMap[$handler] = Eazy::createObject($className, [$action]);
    }

    /**
     * Return controller map.
     * @return array
     */
    public function getControllerMap()
    {
        return $this->_controllerMap;
    }
}