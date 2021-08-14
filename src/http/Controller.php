<?php


namespace toom1996\http;


use toom1996\base\Module;
use YiiS;

class Controller extends Module
{
    public $layout = '@app/views/layouts/main';

    /**
     * @var \toom1996\web\View the view object that can be used to render views or view files.
     */
    private $_view;

    public function runAction($route, $params)
    {

    }

    /**
     *
     * @param $view
     * @param  array  $params
     *
     * @return mixed
     * @throws \ReflectionException
     * @throws \toom1996\base\InvalidConfigException
     */
    public function render($view, $params = [])
    {
        $content = YiiS::$app->getView()->render($view, $params);
        return $this->renderContent($content);
    }


    /**
     *
     * @param $content
     *
     * @return mixed
     * @throws \ReflectionException
     * @throws \toom1996\base\InvalidConfigException
     */
    public function renderContent($content)
    {
        $layoutFile = $this->findLayoutFile(YiiS::$app->getView());
        if ($layoutFile !== false) {
            return YiiS::$app->getView()->renderFile($layoutFile, ['content' => $content], $this);
        }

        return $content;
    }


    /**
     *
     * @param $view \toom1996\http\View
     *
     * @return bool|string
     */
    public function findLayoutFile($view)
    {
        if (is_string($this->layout)) {
            $layout = $this->layout;
        }

        if (!isset($layout)) {
            return false;
        }

        if (strncmp($layout, '@', 1) === 0) {
            $file = YiiS::getAlias($layout);
        }
//        elseif (strncmp($layout, '/', 1) === 0) {
//            $file = YiiS::$app->getLayoutPath() . DIRECTORY_SEPARATOR . substr($layout, 1);
//        } else {
//            $file = $module->getLayoutPath() . DIRECTORY_SEPARATOR . $layout;
//        }

        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
            return $file;
        }
        $path = $file . '.' . $view->defaultExtension;
        if ($view->defaultExtension !== 'php' && !is_file($path)) {
            $path = $file . '.php';
        }

        return $path;
    }
}