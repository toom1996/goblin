<?php


namespace toom1996\base;


use toom1996\helpers\ConsoleHelper;
use toom1996\http\Goblin;

abstract class BaseServer extends Component
{
    /**
     * @var
     */
    public $application;

    /**
     * @var
     */
    public $host;

    /**
     * @var
     */
    public $port;

    /**
     * @var
     */
    public $set;

    public function welcome()
    {
        echo <<<EOL

███████╗ █████╗ ███████╗██╗   ██╗
██╔════╝██╔══██╗╚══███╔╝╚██╗ ██╔╝
█████╗  ███████║  ███╔╝  ╚████╔╝ 
██╔══╝  ██╔══██║ ███╔╝    ╚██╔╝  
███████╗██║  ██║███████╗   ██║   
╚══════╝╚═╝  ╚═╝╚══════╝   ╚═╝  framework version {$this->getEazyVersion()}
                                 
Listen address  {$this->host}
Listen port     {$this->port}
Worker num      {$this->getWorkerNum()}
Task worker num {$this->getTaskWorderNum()}
Daemonize       {$this->getDaemonize()}
Php version     {$this->getPhpVersion()}
Swoole version  {$this->getSwooleVersion()}
EOL;
    }

    /**
     * Return Eazy version.
     * @return string
     */
    public function getEazyVersion()
    {
        return Goblin::getVersion();
    }

    /**
     * Return worker num.
     * @return mixed
     */
    public function getWorkerNum()
    {
        $num = $workerNum = isset($this->set['worker_num'])?:swoole_cpu_num();
        return ConsoleHelper::ansiFormat($num, [ConsoleHelper::BOLD, ConsoleHelper::FG_YELLOW]);
    }

    public function getTaskWorderNum()
    {
        $num = $workerNum = isset($this->set['worker_num'])?:swoole_cpu_num();
        return ConsoleHelper::ansiFormat($num, [ConsoleHelper::BOLD, ConsoleHelper::FG_YELLOW]);
    }

    public function getDaemonize()
    {
        return($this->set['daemonize']) ? 'true' : 'false';
    }

    public function getPhpVersion()
    {
        return PHP_VERSION;
    }

    public function getSwooleVersion()
    {
        return SWOOLE_VERSION;
    }
    
    public function start() {
        var_dump('123');
    }


}