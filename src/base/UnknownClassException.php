<?php


namespace toom1996\base;

/**
 * UnknownClassException represents an exception caused by using an unknown class.
 *
 * @author TOOM <1023150697@qq.com>
 * 
 */
class UnknownClassException extends Exception
{
    
    public function getName()
    {
        return 'Unknown Class';
    }
}