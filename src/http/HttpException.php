<?php


namespace toom1996\http;


use Throwable;
use toom1996\base\Exception;

class HttpException extends Exception
{

    /**
     * HttpException constructor.
     *
     * @param  string  $message
     * @param  int  $code
     * @param  \Throwable|null  $previous
     */
    public function __construct(
        $message = "",
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}