<?php

namespace toom1996\web;

use toom1996\base\Component;
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
     * @param  Request  $request
     *
     * @return bool|false|string
     */
    public function parseRequest($request)
    {
        // The url suffix. (e.g http://abc.com/1.html).
        $suffix = (string) $this->suffix;
        $pathInfo = $request->getPathInfo();
        
        if ($pathInfo !== '/' && $this->suffix !== '') {
            $n = strlen($this->suffix);
            if (substr_compare($pathInfo, $this->suffix, -$n, $n) === 0) {
                $pathInfo = substr($pathInfo, 0, -$n);
                if ($pathInfo === '/') {
                    // suffix alone is not allowed
                    return false;
                }
            }
        }else{
            return $pathInfo;
        }
    }


    /**
     * Parse router for scanner arguments
     * @param  array  $config
     */
    public static function buildRoute(array $config)
    {
        $buildRoute = [];
        foreach ($config['scanner']['arguments'] as $className => $method) {
            foreach ($method as $methodName => $function) {
                if (isset($function['Url'])) {
                    list($verbs, $url) = self::buildUrl($function['Url']);
                    $buildRoute[$url] = [
                        'verbs' => $verbs,
                        'func' => $className . '\\' . $methodName
                    ];
                }
            }
        }
        // overwrite annotation if set urlManager route
        foreach ($config['components']['urlManager']['route'] as $route) {
            list($verbs, $url) = self::buildUrl(key($route));
            $buildRoute[$url] = [
                'verbs' => $verbs,
                'func' => current($route)
            ];
        }

        return $buildRoute;
    }


    /**
     * Build urlManager route
     *
     * @param  array  $function
     *
     * @return array
     */
    private static function buildUrl($function)
    {
        if (is_array($function)) {
            $function = $function[0];
        }
        if (isset($function)) {
            // If has request method (e.g `GET /xxx`)
            if (strpos($function, ' ')) {
                list($verbs, $url) = explode(' ', $function);
            }else{
                list($verbs, $url) = [self::$_verbs, $function];
            }

            // If first letter is not '/'
            if (substr($url,0, 1) !== '/') {
                $url = '/' . $url;
            }
            return [$verbs, $url];
        }else{
            //TODO throw Exception
        }
    }
}