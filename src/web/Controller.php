<?php


namespace toom1996\web;


use toom1996\base\Module;

class Controller extends Module
{
    public function runAction($route)
    {

    }

    public function render($view, $params = [])
    {
        $content = \YiiS::$app->getView()->render($view, $params, $this);
        return $this->renderContent($content);
    }
}