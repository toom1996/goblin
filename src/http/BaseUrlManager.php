<?php


namespace toom1996\http;


use toom1996\base\Component;

/**
 * Class BaseUrlManager
 *
 * @author TOOM <1023150697@qq.com>
 * 
 */
abstract class BaseUrlManager extends Component
{

    /**
     * Route adapter.
     * @var
     */
    protected $adapter;

    /**
     *
     *
     * @return mixed
     */
    abstract protected function parseRequest(): array ;

    /**
     * Returns
     * @param $config
     *
     * @return array
     */
    protected static function getRoute($config): array
    {
        return $config['components']['urlManager']['route'];
    }

    /**
     *
     * @param $config
     *
     * @return mixed
     */
    abstract static function loadRoute($config);
    
}