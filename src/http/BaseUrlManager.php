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
    
}