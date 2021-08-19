<?php


namespace toom1996\base;


class BaseObject
{

    public function __construct()
    {
        $this->init();
    }
    
    public function init()
    {
        echo 123;
    }
    
}