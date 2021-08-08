<?php

namespace toom1996\base;

use app\controllers\SiteController;
use yii\base\Controller;
use yii\base\InvalidRouteException;

/**
 * Class Module
 *
 * @author: TOOM <1023150697@qq.com>
 */
class Module extends Component
{
    /**
     * 默认的应用路由。(e.g. www.xx.com => SiteController)
     * the default route of this application. Defaults to 'site'.
     * @var string
     */
    public $defaultRoute = '@controllers/site/index';

    /**
     * @var array child modules of this module
     */
    private $_modules = [];


    /**
     * @var array mapping from controller ID to controller configurations.
     * Each name-value pair specifies the configuration of a single controller.
     * A controller configuration can be either a string or an array.
     * If the former, the string should be the fully qualified class name of the controller.
     * If the latter, the array must contain a `class` element which specifies
     * the controller's fully qualified class name, and the rest of the name-value pairs
     * in the array are used to initialize the corresponding controller properties. For example,
     *
     * ```php
     * [
     *   'account' => 'app\controllers\UserController',
     *   'article' => [
     *      'class' => 'app\controllers\PostController',
     *      'pageTitle' => 'something new',
     *   ],
     * ]
     * ```
     */
    public $controllerMap = [];

    public $controllerNamespace = 'app\\controllers';

    public function __construct($id = null, $params = null)
    {
        parent::__construct($id, $params);
    }

    public function init()
    {
        if ($this->controllerNamespace === null) {
            $class = get_class($this);
            if (($pos = strrpos($class, '\\')) !== false) {
                $this->controllerNamespace = substr($class, 0, $pos) . '\\controllers';
            }
        }
    }

    public function runAction($route)
    {
        //        var_dump($parts);
//        if (is_array($parts)) {
//            /* @var $controller Controller */
//            list($controller, $actionID) = $parts;
//            $oldController = Yii::$app->controller;
//            Yii::$app->controller = $controller;
//            $result = $controller->runAction($actionID, $params);
//            if ($oldController !== null) {
//                Yii::$app->controller = $oldController;
//            }
//
//            return $result;
//        }
        return $this->createController($route);
//
//        $id = $this->getUniqueId();
//        throw new InvalidRouteException('Unable to resolve the request "' . ($id === '' ? $route : $id . '/' . $route) . '".');
    }


    public function createController($route, $params = [])
    {

        $a = explode('\\', $route);

        $action = array_pop($a);
        $ref = new \ReflectionClass(implode('\\',$a));
        $n = (new (implode('\\',$a)));
        return call_user_func([$n, $action]);
//
//
//        // 根目录
//        if ($route === '/') {
//            $route = $this->defaultRoute;
//        }
//        // double slashes or leading/ending slashes may cause substr problem
//        $route = trim($route, '/');
//        if (strpos($route, '//') !== false) {
//            return false;
//        }
//
//        // 不知道干啥的
//        if (strpos($route, '/') !== false) {
//            list($id, $route) = explode('/', $route, 2);
//        } else {
//            $id = $route;
//            $route = '';
//        }
////        echo ('id -> ' . $id);
////        echo ('route -> ' . $route);
//        // module and controller map take precedence
//        if (isset($this->controllerMap[$id])) {
//            $controller = Toom::createObject($this->controllerMap[$id], [$id, $this]);
//            return [$controller, $route];
//        }
//
//        $module = $this->getModule($id);
////        echo 'module' . PHP_EOL;
////        var_dump($module);
////        if ($module !== null) {
////            return $module->createController($route);
////        }
////
//        if (($pos = strrpos($route, '/')) !== false) {
//            $id .= '/' . substr($route, 0, $pos);
//            $route = substr($route, $pos + 1);
//        }
////
//        $controller = $this->createControllerByID($id);
////        var_dump($controller);
////        var_dump($route);
////        if ($controller === null && $route !== '') {
////            echo 'llll';
////            $controller = $this->createControllerByID($id . '/' . $route);
////            $route = '';
////        }
////
//        return $controller === null ? false : [$controller, $route];
    }

    /**
     * Retrieves the child module of the specified ID.
     * This method supports retrieving both child modules and grand child modules.
     * @param string $id module ID (case-sensitive). To retrieve grand child modules,
     * use ID path relative to this module (e.g. `admin/content`).
     * @param bool $load whether to load the module if it is not yet loaded.
     * @return \yii\base\Module|null the module instance, `null` if the module does not exist.
     * @see hasModule()
     */
    public function getModule($id, $load = true)
    {
        if (($pos = strpos($id, '/')) !== false) {
            // sub-module
            $module = $this->getModule(substr($id, 0, $pos));

            return $module === null ? null : $module->getModule(substr($id, $pos + 1), $load);
        }

        if (isset($this->_modules[$id])) {
            if ($this->_modules[$id] instanceof self) {
                return $this->_modules[$id];
            } elseif ($load) {
                Yii::debug("Loading module: $id", __METHOD__);
                /* @var $module Module */
                $module = Yii::createObject($this->_modules[$id], [$id, $this]);
                $module::setInstance($module);
                return $this->_modules[$id] = $module;
            }
        }

        return null;
    }


    /**
     * Creates a controller based on the given controller ID.
     *
     * The controller ID is relative to this module. The controller class
     * should be namespaced under [[controllerNamespace]].
     *
     * Note that this method does not check [[modules]] or [[controllerMap]].
     *
     * @param string $id the controller ID.
     * @return Controller|null the newly created controller instance, or `null` if the controller ID is invalid.
     * @throws InvalidConfigException if the controller class and its file name do not match.
     * This exception is only thrown when in debug mode.
     */
    public function createControllerByID($id)
    {
        $pos = strrpos($id, '/');
        if ($pos === false) {
            $prefix = '';
            $className = $id;
        } else {
            $prefix = substr($id, 0, $pos + 1);
            $className = substr($id, $pos + 1);
        }

        if ($this->isIncorrectClassNameOrPrefix($className, $prefix)) {
//            echo 'isIncorrectClassNameOrPrefix';
            return null;
        }

        $className = preg_replace_callback('%-([a-z0-9_])%i', function ($matches) {
                return ucfirst($matches[1]);
            }, ucfirst($className)) . 'Controller';
        $className = ltrim($this->controllerNamespace . '\\' . str_replace('/', '\\', $prefix) . $className, '\\');
//        echo 'className' . PHP_EOL;
//        var_dump($className);

        if (strpos($className, '-') !== false || !class_exists($className)) {
            return null;
        }

//        if (is_subclass_of($className, 'yii\base\Controller')) {
//            $controller = Yii::createObject($className, [$id, $this]);
//            return get_class($controller) === $className ? $controller : null;
//        } elseif (YII_DEBUG) {
//            throw new InvalidConfigException('Controller class must extend from \\yii\\base\\Controller.');
//        }

        return null;
    }

    /**
     * Checks if class name or prefix is incorrect
     *
     * @param string $className
     * @param string $prefix
     * @return bool
     */
    private function isIncorrectClassNameOrPrefix($className, $prefix)
    {
        if (!preg_match('%^[a-z][a-z0-9\\-_]*$%', $className)) {
            return true;
        }
        if ($prefix !== '' && !preg_match('%^[a-z0-9_/]+$%i', $prefix)) {
            return true;
        }

        return false;
    }

    /**
     * Returns the directory that contains layout view files for this module.
     * @return string the root directory of layout files. Defaults to "[[viewPath]]/layouts".
     */
    public function getLayoutPath()
    {
        if ($this->_layoutPath === null) {
            $this->_layoutPath = $this->getViewPath() . DIRECTORY_SEPARATOR . 'layouts';
        }

        return $this->_layoutPath;
    }

    /**
     * Returns the directory that contains the view files for this module.
     * @return string the root directory of view files. Defaults to "[[basePath]]/views".
     */
    public function getViewPath()
    {
        if ($this->_viewPath === null) {
            $this->_viewPath = $this->getBasePath() . DIRECTORY_SEPARATOR . 'views';
        }

        return $this->_viewPath;
    }

    /**
     * Returns the root directory of the module.
     * It defaults to the directory containing the module class file.
     * @return string the root directory of the module.
     */
    public function getBasePath()
    {
        if ($this->_basePath === null) {
            $class = new \ReflectionClass($this);
            $this->_basePath = dirname($class->getFileName());
        }

        return $this->_basePath;
    }
}