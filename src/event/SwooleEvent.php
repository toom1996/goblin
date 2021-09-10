<?php


namespace toom1996\event;


class SwooleEvent
{
    const SWOOLE_ON_WORKER_START = 'workerStart';
    
    const SWOOLE_ON_REQUEST = 'request';

    /**
     * Swoole event `start`.
     * For detailed usage, please check https://wiki.swoole.com/#/server/events?id=onstart.
     */
    const SWOOLE_ON_START = 'start';
}