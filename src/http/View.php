<?php


namespace toom1996\http;


use Throwable;
use toom1996\base\Component;
use toom1996\base\InvalidCallException;
use toom1996\base\InvalidConfigException;
use toom1996\helpers\FileHelper;

/**
 * Class View
 *
 * @author: TOOM1996
 */
class View extends Component
{
    /**
     * Default render file extension.
     * @var string
     */
    public $defaultExtension = 'php';

    /**
     * Render html title.
     * @var
     */
    public $title;

    
    public $assetBundles = [];

    /**
     *
     * @param  string  $view
     * @param  array   $params
     *
     * @return false|string
     * @throws Throwable
     */
    public function render(string $view, array $params = [])
    {
        $viewFile = $this->findViewFile($view);
        return $this->renderFile($viewFile, $params);
    }

    /**
     *
     * @param $view
     *
     * @return bool|string
     */
    public function findViewFile($view)
    {
        if (strncmp($view, '@', 1) === 0) {
            // e.g. "@app/views/main"
            $file = Eazy::getAlias($view);
        } elseif (strncmp($view, '/', 1) === 0) {
            // e.g. "/site/index"
//            if (YiiS::$app->controller !== null) {
//                $file = Yii::$app->controller->module->getViewPath() . DIRECTORY_SEPARATOR . ltrim($view, '/');
//            } else {
//                throw new InvalidCallException("Unable to locate view file for view '$view': no active controller.");
//            }
        } else {
            throw new InvalidCallException("Unable to resolve view file for view '$view': no active view context.");
        }

        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
            return $file;
        }
        $path = $file . '.' . $this->defaultExtension;
        if ($this->defaultExtension !== 'php' && !is_file($path)) {
            $path = $file . '.php';
        }

        return $path;
    }

    /**
     *
     * @param $viewFile
     * @param $params
     * @param  null  $context
     *
     * @return false|string
     * @throws Throwable
     */
    public function renderFile($viewFile, $params, $context = null)
    {
        $viewFile = $requestedFile = Eazy::getAlias($viewFile);
        if (!is_file($viewFile)) {
            throw new ViewNotFoundException("The view file does not exist: $viewFile");
        }

//        $oldContext = $this->context;
//        if ($context !== null) {
//            $this->context = $context;
//        }
        $output = '';
//        $this->_viewFiles[] = [
//            'resolved' => $viewFile,
//            'requested' => $requestedFile
//        ];

        $output = $this->renderPhpFile($viewFile, $params);
//        array_pop($this->_viewFiles);
//        $this->context = $oldContext;

        return $output;
    }

    /**
     *
     * @param $_file_
     * @param  array  $_params_
     *
     * @return false|string
     * @throws Throwable
     */
    public function renderPhpFile($_file_, $_params_ = [])
    {
        $_obInitialLevel_ = ob_get_level();
        ob_start();
        ob_implicit_flush(false);
        extract($_params_, EXTR_OVERWRITE);
        try {
            require $_file_;
            return ob_get_clean();
        }catch (Throwable $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        }
    }

    public function registerAssetBundle($name, $position = null)
    {
        if (!isset($this->assetBundles[$name])) {
            $am = Eazy::$app->getAssetManager();
            $bundle = $am->getBundle($name);
            $this->assetBundles[$name] = false;
            // register dependencies
            $pos = isset($bundle->jsOptions['position']) ? $bundle->jsOptions['position'] : null;
            foreach ($bundle->depends as $dep) {
                $this->registerAssetBundle($dep, $pos);
            }
            $this->assetBundles[$name] = $bundle;
        } elseif ($this->assetBundles[$name] === false) {
            throw new InvalidConfigException("A circular dependency is detected for bundle '$name'.");
        } else {
            $bundle = $this->assetBundles[$name];
        }

        if ($position !== null) {
            $pos = isset($bundle->jsOptions['position']) ? $bundle->jsOptions['position'] : null;
            if ($pos === null) {
                $bundle->jsOptions['position'] = $pos = $position;
            } elseif ($pos > $position) {
                throw new InvalidConfigException("An asset bundle that depends on '$name' has a higher javascript file position configured than '$name'.");
            }
            // update position for all dependencies
            foreach ($bundle->depends as $dep) {
                $this->registerAssetBundle($dep, $pos);
            }
        }

        return $bundle;
    }
}