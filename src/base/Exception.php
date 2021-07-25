<?php


namespace toom1996\base;

/**
 * 
 * The base exception class.
 *
 * @author TOOM <1023150697@qq.com>
 * 
 */
class Exception extends \Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Exception';
    }
}