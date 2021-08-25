<?php


namespace toom1996\http;


use toom1996\base\Component;
use toom1996\base\InvalidConfigException;
use toom1996\base\Module;

class Controller extends Component
{
    public $layout = '@app/views/layouts/main';

    public $actionId;

    public function __construct($actionId)
    {
        echo '-----';
        $this->actionId = $actionId;
        var_dump($actionId);
    }

    /**
     *
     *
     * @param         $view
     * @param  array  $params
     *
     * @return mixed
     * @throws \ReflectionException
     * @throws \Throwable
     * @throws InvalidConfigException
     */
    public function render($view, $params = [])
    {
        $content = Goblin::$app->getView()->render($view, $params);
        return $this->renderContent($content);
    }


    /**
     *
     *
     * @param $content
     *
     * @return false|string
     * @throws InvalidConfigException
     * @throws \Throwable
     */
    public function renderContent($content)
    {
        $layoutFile = $this->findLayoutFile(Goblin::$app->getView());
        if ($layoutFile !== false) {
            return Goblin::$app->getView()->renderFile($layoutFile, ['content' => $content], $this);
        }

        return $content;
    }


    /**
     *
     *
     * @param $view View
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
            $file = Goblin::getAlias($layout);
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