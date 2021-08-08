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
    public $adapter;

    /**
     *
     *
     * @return mixed
     */
    abstract protected function parseRequest(): array ;
    
    
    protected static function getRoute($config): array
    {
        return $config['components']['urlManager']['route'];
    }
    
}