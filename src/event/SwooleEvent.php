<?php


namespace toom1996\event;

/**
 * Class SwooleEvent
 *
 * @author: TOOM1996
 * @since 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class SwooleEvent
{
    /**
     * Swoole event `onWorkerStart`.
     * For details, please see https://wiki.swoole.com/#/server/events?id=onworkerstart.
     */
    const SWOOLE_ON_WORKER_START = 'workerStart';

    /**
     * Swoole event `onRequest`.
     * For details, please see https://wiki.swoole.com/#/websocket_server?id=onrequest.
     */
    const SWOOLE_ON_REQUEST = 'request';

    /**
     * Swoole event `onStart`.
     * For details, please see https://wiki.swoole.com/#/server/events?id=onstart.
     */
    const SWOOLE_ON_START = 'start';
}