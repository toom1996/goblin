<?php

namespace toom1996\web;

use toom1996\base\Component;

/**
 * Class UrlManager
 *
 * @author: TOOM <1023150697@qq.com>
 */
class UrlManager extends Component
{

    public $rules = [];


    public $suffix;

    /**
     *
     * @param Request $request
     */
    public function parseRequest($request)
    {
//        /* @var $rule UrlRule */
//        foreach ($this->rules as $rule) {
//            $result = $rule->parseRequest($this, $request);
//            if (YII_DEBUG) {
//                Yii::debug([
//                    'rule' => method_exists($rule, '__toString') ? $rule->__toString() : get_class($rule),
//                    'match' => $result !== false,
//                    'parent' => null,
//                ], __METHOD__);
//            }
//            if ($result !== false) {
//                return $result;
//            }
//        }

        $suffix = (string) $this->suffix;
        $pathInfo = $request->getPathInfo();

        var_dump('path info', $pathInfo);

        // pathInfo 应该永远不会为空
        if ($suffix !== '' && $pathInfo !== '') {
            $n = strlen($this->suffix);
            if (substr_compare($pathInfo, $this->suffix, -$n, $n) === 0) {
                $pathInfo = substr($pathInfo, 0, -$n);
                if ($pathInfo === '/') {
                    // suffix alone is not allowed
                    return false;
                }
            } else {
                // suffix doesn't match
                return false;
            }
        }
//
//        if ($normalized) {
//            // pathInfo was changed by normalizer - we need also normalize route
//            return $this->normalizer->normalizeRoute([$pathInfo, []]);
//        }
//
        return $pathInfo;
    }
}