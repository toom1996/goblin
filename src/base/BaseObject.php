<?php


namespace toom1996\base;


use toom1996\http\Eazy;

class BaseObject
{

    /**
     * BaseObject constructor.
     *
     * @param  array  $config
     */
    public function __construct($config = [])
    {
        // TODO configure attributes.
        if (!empty($config)) {
            Eazy::configure($this, $config);
        }
        $this->init();
    }
    
    public function init()
    {
    }
    
}