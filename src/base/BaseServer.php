<?php


namespace toom1996\base;


use Swoole\Http\Server;
use toom1996\helpers\ConsoleHelper;
use toom1996\http\Goblin;

abstract class BaseServer extends Component
{
    public $server;

    /**
     * Server host.
     * @var string
     */
    public string $host;

    /**
     * Server port.
     * @var int
     */
    public int $port;

    /**
     * Server setting.
     * @var array
     */
    public array $setting = [];

    /**
     * Server event.
     * @var array
     */
    public array $event = [];


    public function init()
    {
        $this->server->set($this->setting);
    }
}