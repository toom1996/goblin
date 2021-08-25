<?php

namespace toom1996\base;

use toom1996\http\Controller;
use toom1996\http\Goblin;
/**
 * Class Module
 *
 * @author: TOOM <1023150697@qq.com>
 */
class Module extends ServiceLocator
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

    /**
     * Run action.
     *
     * @param $handler
     *
     * @return mixed
     * @throws \ReflectionException
     * @throws \toom1996\base\UnknownClassException
     * @throws \toom1996\base\InvalidConfigException
     */
    public function runAction($path)
    {
        // If is register
        if (isset(Goblin::$handlerMap[$path])) {
            $controller = Goblin::$handlerMap[$path];
        }else{
            $controller = Goblin::createController($path, true);
        }

        if (is_object($controller) && $controller instanceof Controller) {
            return call_user_func([$controller, $controller->actionId]);
        }

        throw new InvalidConfigException("Unknown action.");
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

    /**
     *
     *
     * @param        $actionPath
     *
     * @param  bool  $setToHandlerMap
     *
     * @throws \ReflectionException
     */
    public static function createController($actionPath, $setToHandlerMap = true)
    {
        // If route is `@controllers/site/index`, will be convert @controller to BathPath
        $handlerAlias = Goblin::getAlias($actionPath);
        $ex = explode('/', $handlerAlias);

        // Find controller and action.
        [$controller, $action] = array_slice($ex, -2, 2);

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

        if (!file_exists($handlerFile)) {
            throw new UnknownClassException("{Unknown class {$actionPath}");
        }
        
        $classNamespace = Goblin::getNamespace($handlerFile);
        $className = '\\' . $classNamespace . '\\' . basename(str_replace('.php', '', $handlerFile));

        $ref = new \ReflectionClass($className);
        if (!$ref->hasMethod($action)) {
            throw new InvalidConfigException("class {$className} does not have a method {$action}, please check your config.");
        }

        // Create controller object.
        $controllerInstance = Goblin::createObject($className, [$action]);

        /**
         * Set to handlerMap.
         * @see Goblin::$handlerMap
         */
        if ($setToHandlerMap) {
            Goblin::setHandlerMap($actionPath, $controllerInstance);
        }
        return $controllerInstance;
    }
}