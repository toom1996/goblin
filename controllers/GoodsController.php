<?php


namespace app\controllers;


use app\assets\GoodsAsset;
use yii\web\Controller;

/**
 * 商品类
 * Class GoodsController
 *
 * @author: TOOM <1023150697@qq.com>
 */
class GoodsController
{


    #[Url ("goods/man")]
    public function actionMan()
    {
        return $this->render('man');
    }

    #[Url ("goods")]
    public function actionIndex()
    {
        return $this->render('index');
    }
}