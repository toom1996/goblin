<?php

namespace toom1996\base;

class ErrorHandler
{

    public function handleException($exception)
    {
        var_dump($exception);
    }
}