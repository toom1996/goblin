<?php


namespace toom1996\web;


use toom1996\base\Exception;

/**
 * Class UrlNormalizerRedirectException
 *
 * @author: TOOM <1023150697@qq.com>
 */
class UrlNormalizerRedirectException extends Exception
{

    /**
     * @var array|string the parameter to be used to generate a valid URL for redirection
     * @see [[\yii\helpers\Url::to()]]
     */
    public $url;
    /**
     * @var bool|string the URI scheme to use in the generated URL for redirection
     * @see [[\yii\helpers\Url::to()]]
     */
    public $scheme;
    /**
     * @var int the HTTP status code
     */
    public $statusCode;


    /**
     * @param array|string $url the parameter to be used to generate a valid URL for redirection.
     * This will be used as first parameter for [[\yii\helpers\Url::to()]]
     * @param int $statusCode HTTP status code used for redirection
     * @param bool|string $scheme the URI scheme to use in the generated URL for redirection.
     * This will be used as second parameter for [[\yii\helpers\Url::to()]]
     * @param string $message the error message
     * @param int $code the error code
     * @param \Exception $previous the previous exception used for the exception chaining
     */
    public function __construct($url, $statusCode = 302, $scheme = false, $message = null, $code = 0, \Exception $previous = null)
    {
        $this->url = $url;
        $this->scheme = $scheme;
        $this->statusCode = $statusCode;
        parent::__construct($message, $code, $previous);
    }
}