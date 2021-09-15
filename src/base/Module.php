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
        if (isset(Eazy::$app->getUrlManager()->controllerMap[$path])) {
            $controller = Eazy::$app->getUrlManager()->controllerMap[$path];
        }else{
            $controller = Eazy::$app->getUrlManager()->setControllerMap($path);
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
}