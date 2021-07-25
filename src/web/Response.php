<?php

namespace toom1996\web;

/**
 * Class Response
 *
 * @author: TOOM <1023150697@qq.com>
 */
class Response
{

    public $fd = 0;

    public $socket;

    public $header;

    public $cookie;

    public $trailer;

    public $content;

    public function send(\Swoole\Http\Response $response)
    {
//        var_dump(Toom::$_app);
        return $response->end($this->sendContent());
    }

    public function sendContent()
    {
        return 'send content';
    }

}