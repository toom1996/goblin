<?php


namespace toom1996\controllers;


use toom1996\http\Controller;
use toom1996\http\Goblin;

class ErrorHandlerController extends Controller
{

    public function actionError()
    {
        if (Goblin::$app->getErrorHandler()->exception === null) {
            die('123');
        }

        return $this->render('@goblin/views/errorHandler/exception', [
            'exception' => Goblin::$app->getErrorHandler()->exception,
            'handler' => Goblin::$app->getErrorHandler(),
        ]);
    }
}