<?php


namespace toom1996\base;


use toom1996\base\Exception;

/**
 * Class HeadersAlreadySentException
 *
 * @author TOOM <1023150697@qq.com>
 * 
 */
class HeadersAlreadySentException extends Exception
{
    /**
     * {@inheritdoc}
     */
    public function __construct($file, $line)
    {
        parent::__construct($message);
    }
}