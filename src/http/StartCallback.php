<?php


namespace toom1996\http;

use toom1996\base\BaseConsole;
use toom1996\base\Stdout;
use toom1996\helpers\ConsoleHelper;

/**
 * Class StartCallback
 *
 * @author: TOOM1996
 * @since 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class StartCallback extends BaseConsole
{
    public static function onStart(\Swoole\Server $server)
    {
        Stdout::info('Eazy framework is running!');
        // https://wiki.swoole.com/#/functions?id=swoole_set_process_name
        swoole_set_process_name("Master {$server->master_pid}");
    }
}