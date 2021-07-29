<?php

namespace toom1996\web;

use Psr\Container\ContainerInterface;
use toom1996\base\Component;
use toom1996\base\NotFoundHttpException;
use toom1996\helpers\BaseFileHelper;
use yii\base\InvalidConfigException;
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
                    list($verbs, $url) = self::buildNode($function['Url']);
                    $buildRoute[$url] = [
                        'verbs' => $verbs,
                        'func' => $className . '\\' . $methodName
                    ];
                }
            }
        }
        // overwrite annotation if set urlManager route
        foreach ($config['components']['urlManager']['route'] as $routes) {

            foreach ($routes as $route => $path) {
                if (is_array($path)) {
                    // Route group
                    foreach ($path as $childRoute => $childPath) {
                        list($verbs, $url) = self::buildNode($childRoute);
                        $buildRoute[self::trimSlashes($route . $url)] = [
                            'verbs' => $verbs,
                            'func' => $childPath
                        ];
                    }
                }else{
                    list($verbs, $url) = self::buildNode($route);
                    $buildRoute[$url] = [
                        'verbs' => $verbs,
                        'func' => $path
                    ];
                }
            }

//            foreach ($routes as $route) {
//                var_dump($route);
//                list($verbs, $url) = self::buildNode(key($route));
//                $buildRoute[$url] = [
//                    'verbs' => $verbs,
//                    'func' => current($route)
//                ];
//            }
        }

        var_dump($buildRoute);
        return $buildRoute;
    }


    /**
     * Build urlManager route
     *
     * @param $route
     *
     * @return array
     */
    private static function buildNode($route) : array
    {
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
}