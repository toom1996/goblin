<?php

namespace toom1996\http;

use toom1996\base\BootstrapInterface;

#[Http (name: "http")]
class Http implements BootstrapInterface
{
    
    public function bootstrap()
    {
        // TODO: Implement execute() method.
        echo 123123;
    }
    
    public function getId()
    {
        // TODO: Implement getId() method.
        return 123;
    }
}