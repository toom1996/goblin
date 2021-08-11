<?php

namespace toom1996\base;

/**
 * Class ErrorHandler
 *
 * @author: TOOM <1023150697@qq.com>
 */
abstract class ErrorHandler extends Component
{
    public function handleException($exception){}

    abstract protected function renderException($exception);
}