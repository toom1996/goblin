<?php


namespace toom1996\controllers;


use toom1996\base\InvalidConfigException;
use toom1996\http\Controller;
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
        return $this->render('@goblin/views/errorHandler/exception', [
            'exception' => Goblin::$app->getErrorHandler()->exception,
            'handler' => Goblin::$app->getErrorHandler(),
        ]);
    }
}