<?php

namespace toom1996\base;

use toom1996\http\Controller;
use toom1996\http\Eazy;
use toom1996\http\Goblin;
/**
 * Class Module
 *
 * @author: TOOM <1023150697@qq.com>
 */
class Module extends ServiceLocator
{

    /**
     * Run action.
     *
     * @param $path
     *
     * @return mixed
     * @throws InvalidConfigException
     * @throws \ReflectionException
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
     * @param $actionPath
     * @param  bool  $setToHandlerMap
     *
     * @return object
     * @throws \ReflectionException
     * @throws \toom1996\base\InvalidConfigException
     * @throws \toom1996\base\UnknownClassException
     */
    public static function createController($actionPath, $setToHandlerMap = true)
    {
        // If route is `@controllers/site/index`, will be convert @controller to BathPath
        $handlerAlias = Eazy::getAlias($actionPath);
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
        var_dump('@@@@@@@@@@@@@@@@');
        var_dump($handlerFile);
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
        $controllerInstance = Eazy::createObject($className, [$action]);

        /**
         * Set to handlerMap.
         * @see Goblin::$handlerMap
         */
        if ($setToHandlerMap) {
            Eazy::setHandlerMap($actionPath, $controllerInstance);
        }
        return $controllerInstance;
    }
}