<?php

namespace toom1996\web;

use Psr\Container\ContainerInterface;
use toom1996\base\Component;
use toom1996\base\NotFoundHttpException;
use toom1996\helpers\BaseArrayHelper;
use toom1996\helpers\BaseFileHelper;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\UrlRule;
use yii\web\UrlRuleInterface;

/**
 * Class UrlManager
 *
 * @author: TOOM <1023150697@qq.com>
 */
class UrlManager extends Component
{
    private static $_verbs = 'GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS';


    public $rules = [

    ];

    public $route = [];


    public $suffix = '';

    /**
     * Init UrlManager
     */
    public function init()
    {
        parent::init();
    }

    /**
     * /hello/{id}/{sdfsdf}/
     *
     * @param  Request  $request
     *
     * @return bool|false|string
     * @throws \toom1996\base\NotFoundHttpException
     */
    public function parseRequest($request)
    {
        // The url suffix. (e.g http://abc.com/1.html).
        $suffix = (string) $this->suffix;
        $pathInfo = $request->getPathInfo();

//        if ($pathInfo !== '/' && $this->suffix !== '') {
//            $n = strlen($this->suffix);
//            if (substr_compare($pathInfo, $this->suffix, -$n, $n) === 0) {
//                $pathInfo = substr($pathInfo, 0, -$n);
//                if ($pathInfo === '/') {
//                    // suffix alone is not allowed
//                    return false;
//                }
//            }
//        }

        var_dump(\YiiS::$app->request);
        $this->matchRoute($pathInfo);
        if (!isset($this->route[$pathInfo])) {
            // TODO new Exception
            throw new NotFoundHttpException("Page not found~");
        }

        var_dump($this->route[$pathInfo]);
        var_dump($pathInfo);
        return $pathInfo;
    }


    /**
     * Build a route tree
     *
     * @param  array  $config
     *
     * @return array
     */
    public static function buildRouteTree(array $config)
    {
        $buildRoute = [];
        foreach ($config['scanner']['arguments'] as $className => $method) {
            foreach ($method as $methodName => $function) {
                if (isset($function['Url'])) {
                    $node = self::buildNode(self::parseRoute($function['Url']), $className . '\\' . $methodName);
                    $buildRoute = BaseArrayHelper::merge($buildRoute, $node);
                }
            }
        }
        // overwrite annotation if set urlManager route
        foreach ($config['components']['urlManager']['route'] as $routes) {
            foreach ($routes as $route => $path) {
                if (is_array($path)) {
                    // Route group
                    foreach ($path as $childRoute => $method) {
                        list($verbs, $url) = self::parseRoute($childRoute);
                        $node = self::buildNode([$verbs, $route . $url], $method);
                        $buildRoute = BaseArrayHelper::merge($buildRoute, $node);
                    }
                }else{
                    $node = self::buildNode(self::parseRoute($route), $path);
                    $buildRoute = BaseArrayHelper::merge($buildRoute, $node);
                }
            }
        }
        return $buildRoute;
    }


    /**
     * Build urlManager route.
     * It can return route verbs and trim url.
     *
     * @param $route `(e.g GET /xxx)`
     *
     * @return array
     */
    private static function parseRoute($route) : array
    {
        // Compatible with routing in the configuration
        if (is_array($route)) {
            if (isset($route[0])) {
                $route = $route[0];
            }else{
                // TODO throw exception 'undefined url'
            }
        }
        if (!$route) {
            //TODO throw Exception 'url error'
        }

        // If has request method (e.g `GET /xxx`)
        if (strpos($route, ' ')) {
            list($verbs, $url) = explode(' ', $route);
        }else{
            list($verbs, $url) = [self::$_verbs, $route];
        }

        // If first letter is not '/'
        if (substr($url,0, 1) !== '/') {
            $url = '/' . $url;
        }

        return [$verbs, self::trimSlashes($url)];
    }


    /**
     * Trim url slashes.
     * `/xxx/xxx////1///xxx` will be trim to `/xxx/xxx/1/xxx`
     * @param $url
     *
     * @return string
     */
    private static function trimSlashes($url): string
    {
        if ($url !== '/') {
            $url = rtrim($url, '/');
        }
        return preg_replace('#/+#', '/', $url);
    }

    /**
     * Build route tree node.
     * When route is /xx/dd/ff. it's will be build to ` xx => [ dd => [ ff => [] ]]`
     *
     * @param $url
     *
     * @param $class
     *
     * @return array
     */
    private static function buildNode($url, $class): array
    {
        if (!is_array($url)) {
            // TODO throw new Exception
        }
        list($verbs, $url) = $url;
        $ex = array_filter(explode('/', $url));
        $node = $tmp = [];
        foreach ($ex as $pattern) {
            if ($pattern) {
                array_push($tmp, $pattern);
                $path = implode('@', $tmp);
                if (end($ex) === $pattern) {
                    BaseArrayHelper::setValue($node, "{$path}", [
                        'verbs' => $verbs,
                        'method' => $class,
                    ], '@');
                }else{
                    BaseArrayHelper::setValue($node, "{$path}", [], '@');
                }
            }
        }
        return $node;
    }


    /**
     * Match route.
     * @param $route
     *
     * @throws \Exception
     */
    private function matchRoute($route)
    {
        // Match url manager route
        $pattern = explode('/', ltrim($route, '/'));
        if (isset(BaseArrayHelper::getValue($this->route, $pattern)['method'])) {
            echo BaseArrayHelper::getValue($this->route, $pattern)['method'];
        }
        // Match url manager route with preg
        $tmp = [];
        foreach ($pattern as $k => $p) {
            $tmp[] = $p;
            if (!BaseArrayHelper::getValue($this->route, $tmp)) {
                $c = $tmp;
                array_pop($c);
                var_dump(BaseArrayHelper::getValue($this->route, end($tmp)));
                var_dump(BaseArrayHelper::getValue($this->route, $c));
                foreach (BaseArrayHelper::getValue($this->route, $c) as $preg => $value) {
                    preg_match('/<.*:.*>/', $preg, $res);
                    if (isset($res[0])) {
                        preg_match("/{$res[0]}/", $preg, $pattern);
                        var_dump("/{$res[0]}/");
                        var_dump($pattern);
                    }
                }

                var_dump($c);
                echo '没找到' . $p;
            }
        }

        // Match url manager route with action
    }
}