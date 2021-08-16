<?php

namespace toom1996\base;

use yii\base\Controller;
use YiiS;
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
    public function runAction($handler)
    {
        if (isset(YiiS::$handlerMap[$handler])) {
            $handlerMap = YiiS::$handlerMap[$handler];
        }else{
            throw new InvalidConfigException("{$handler} is invalid function, please check your config.");
        }
        if (!class_exists($handlerMap['class'])) {
            throw new UnknownClassException("{Unknown class {$handlerMap['class']}");
        }
        $ref = new \ReflectionClass($handlerMap['class']);
        if (!$ref->hasMethod($handlerMap['action'])) {
            throw new InvalidConfigException("class {$handlerMap['class']} does not have a method {$handlerMap['action']}, please check your config.");
        }
        echo '789';
        return call_user_func([$ref->newInstance(), $handlerMap['action']]);
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