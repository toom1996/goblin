<?php


namespace toom1996\web;


use toom1996\base\Component;
use YiiS;

/**
 * Class View
 *
 * @author: TOOM <1023150697@qq.com>
 */
class View extends Component
{

    /**
     *
     * @param  string  $view The view name.
     * @param  array  $params The parameters (name-value pairs) that will be extracted and made available in the view file.
     */
    public function render(string $view, array $params = [])
    {
        echo 'render';
        $viewFile = $this->findViewFile($view);
        return $this->renderFile($viewFile, $params);
    }


    public function findViewFile($view)
    {
        var_dump($view);
//        if (strncmp($view, '@', 1) === 0) {
//            // e.g. "@app/views/main"
//            $file = YiiS::getAlias($view);
//        } elseif (strncmp($view, '//', 2) === 0) {
//            // e.g. "//layouts/main"
//            $file = YiiS::$app->getViewPath() . DIRECTORY_SEPARATOR . ltrim($view, '/');
//        } elseif (strncmp($view, '/', 1) === 0) {
//            // e.g. "/site/index"
//            if (YiiS::$app->controller !== null) {
//                $file = Yii::$app->controller->module->getViewPath() . DIRECTORY_SEPARATOR . ltrim($view, '/');
//            } else {
//                throw new InvalidCallException("Unable to locate view file for view '$view': no active controller.");
//            }
//        } elseif ($context instanceof ViewContextInterface) {
//            $file = $context->getViewPath() . DIRECTORY_SEPARATOR . $view;
//        } elseif (($currentViewFile = $this->getRequestedViewFile()) !== false) {
//            $file = dirname($currentViewFile) . DIRECTORY_SEPARATOR . $view;
//        } else {
//            throw new InvalidCallException("Unable to resolve view file for view '$view': no active view context.");
//        }
//
//        var_dump($file);
//
//        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
//            return $file;
//        }
//        $path = $file . '.' . $this->defaultExtension;
//        if ($this->defaultExtension !== 'php' && !is_file($path)) {
//            $path = $file . '.php';
//        }
//
//        return $path;
    }

    protected function renderFile($viewFile, $params)
    {

    }
}