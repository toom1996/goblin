<?php


namespace toom1996\base;

use Throwable;

/**
 * NotFoundHttpException represents an exception caused by using an not not found http route.
 *
 * @author: TOOM <1023150697@qq.com>
 */
class NotFoundHttpException extends Exception
{

    /**
     * NotFoundHttpException constructor.
     *
     * @param  string  $message Error message
     * @param  int  $code Default Error code for 404
     * @param  \Throwable|null  $previous
     */
    public function __construct(
        $message = "",
        $code = 404,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}