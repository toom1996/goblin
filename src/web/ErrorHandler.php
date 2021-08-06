<?php

namespace toom1996\web;

class ErrorHandler extends \toom1996\base\ErrorHandler
{
    /**
     * ErrorHandler render error page action.
     * @var
     */
    public $errorAction;

    public function handleException($exception)
    {

        if (error_reporting()) {

        }
        var_dump(debug_backtrace());
        var_dump($exception);
    }
}