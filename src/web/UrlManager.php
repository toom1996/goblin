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
    public $enablePrettyUrl = false;

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

//        $this->rules = $this->buildRules($this->rules);

    }

    /**
     * Builds URL rule objects from the given rule declarations.
     *
     * @param array $ruleDeclarations the rule declarations. Each array element represents a single rule declaration.
     * Please refer to [[rules]] for the acceptable rule formats.
     * @return UrlRuleInterface[] the rule objects built from the given rule declarations
     * @throws InvalidConfigException if a rule declaration is invalid
     */
    protected function buildRules($ruleDeclarations)
    {
        $builtRules = [];
        $verbs = 'GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS';
        foreach ($ruleDeclarations as $key => $rule) {
            if (is_string($rule)) {
                $rule = ['route' => $rule];
                if (preg_match("/^((?:($verbs),)*($verbs))\\s+(.*)$/", $key, $matches)) {
                    $rule['verb'] = explode(',', $matches[1]);
                    // rules that are not applicable for GET requests should not be used to create URLs
                    if (!in_array('GET', $rule['verb'], true)) {
                        $rule['mode'] = UrlRule::PARSING_ONLY;
                    }
                    $key = $matches[4];
                }
                $rule['pattern'] = $key;
            }
            if (is_array($rule)) {
                $rule = Yii::createObject(array_merge($this->ruleConfig, $rule));
            }
            if (!$rule instanceof UrlRuleInterface) {
                throw new InvalidConfigException('URL rule class must implement UrlRuleInterface.');
            }
            $builtRules[] = $rule;
        }

        $this->setBuiltRulesCache($ruleDeclarations, $builtRules);

        return $builtRules;
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
            return false;
        }
        return $pathInfo;
    }


    /**
     * Parse router for scanner arguments
     * @param  array  $config
     */
    public static function buildRoute(array $config)
    {
        $urlManagerRoute = [];
        $verbs = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
        $route = [];
        foreach ($config['scanner']['arguments'] as $className => $method) {
            foreach ($method as $methodName => $function) {
                foreach ($function as $annotationName => $arguments) {
                    if ($annotationName === 'Url') {
                        $route = explode(' ', current($arguments));
                        if (count($route) >= 2) {
                            list($verb, $url) = $route;
                        }else{
                            $url = $route;
                        }
                        var_dump($url);
                        $route[$url] = [
                            'class' => $className,
                            'func' => $methodName,
                            'url' => $function['Url'],
                            'verbs' => $verb ?? $verbs,
                        ];
                    }
                }
            }
        }

        var_dump($route);
//        // overwrite annotation if set urlManager route
//        foreach ($config['components']['urlManager']['route'] as $route) {
//            list($verb, $url) = explode(' ', key($route));
//            var_dump($verb);
//            var_dump($url);
//        }
    }
}