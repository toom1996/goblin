<?php


namespace toom1996\controllers;


use toom1996\base\InvalidConfigException;
use toom1996\http\Controller;
use toom1996\http\Eazy;
use toom1996\http\Goblin;

class ErrorHandlerController extends Controller
{

    /**
     *
     *
     * @return mixed
     * @throws \ReflectionException
     * @throws \Throwable
     * @throws InvalidConfigException
     */
    public function actionError()
    {
        return $this->render('@eazy/views/errorHandler/exception', [
            'exception' => Eazy::$app->getErrorHandler()->exception,
            'handler' => Eazy::$app->getErrorHandler(),
        ]);
    }
}