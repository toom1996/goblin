<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController
{
    public function __construct()
    {
        echo 'OOOOOKKKKKKKKK';
    }

    public function actionIndex()
    {

    }

    #[Url ('GET /post')]
    #[Xd ('000')]
    public function actionPost()
    {

    }
}
