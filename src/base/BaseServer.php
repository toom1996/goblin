<?php


namespace toom1996\base;


use Swoole\Http\Server;
use toom1996\helpers\ConsoleHelper;
use toom1996\http\Goblin;

abstract class BaseServer extends Component
{
    /**
     * 
     * @var Server
     */
    public $server;
    
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
    public $setting = [];
    
    
    public $config;

    public function init()
    {
        $this->server->set($this->setting);
    }

    public function welcome()
    {
        ConsoleHelper::beginAnsiFormat([ConsoleHelper::BOLD, ConsoleHelper::FG_YELLOW]);
        echo <<<EOL

███████╗ █████╗ ███████╗██╗   ██╗
██╔════╝██╔══██╗╚══███╔╝╚██╗ ██╔╝
█████╗  ███████║  ███╔╝  ╚████╔╝ 
██╔══╝  ██╔══██║ ███╔╝    ╚██╔╝  
███████╗██║  ██║███████╗   ██║   
╚══════╝╚═╝  ╚═╝╚══════╝   ╚═╝   

EOL;
        ConsoleHelper::endAnsiFormat();
        echo <<<EOL
{$this->consoleString('Eazy version')}    {$this->getEazyVersion()}
{$this->consoleString('Listen address')}  {$this->consoleString($this->host, [ConsoleHelper::BOLD, ConsoleHelper::FG_GREEN])}
{$this->consoleString('Listen port')}     {$this->consoleString($this->port, [ConsoleHelper::BOLD, ConsoleHelper::FG_GREEN])}
{$this->consoleString('Worker num')}      {$this->getWorkerNum()}
{$this->consoleString('Task worker num')} {$this->getTaskWorderNum()}
{$this->consoleString('Daemonize')}       {$this->getDaemonize()}
{$this->consoleString('Php version')}     {$this->getPhpVersion()}
{$this->consoleString('Swoole version')}  {$this->getSwooleVersion()}
EOL;
    }

    /**
     * Return Eazy version.
     * @return string
     */
    public function getEazyVersion()
    {
        return $this->consoleString(Eazy::getVersion(),[ConsoleHelper::BOLD, ConsoleHelper::FG_GREEN]);
    }

    /**
     * Return worker num.
     * @return mixed
     */
    public function getWorkerNum()
    {
        $swooleCpuNum = swoole_cpu_num();
        $workerNum = isset($this->set['worker_num']) ? $this->set['worker_num'] : swoole_cpu_num();
        if ($workerNum > $swooleCpuNum * 1000) {
            return $this->consoleString($workerNum, [ConsoleHelper::BOLD, ConsoleHelper::FG_RED]);
        }else{
            return $this->consoleString($workerNum, [ConsoleHelper::BOLD, ConsoleHelper::FG_GREEN]);
        }
    }

    /**
     * Return task worker num.
     * @return string
     */
    public function getTaskWorderNum()
    {
        $num = $workerNum = isset($this->set['task_worker_num']) ? $this->set['task_worker_num'] : swoole_cpu_num();
        return $this->consoleString($num, [ConsoleHelper::BOLD, ConsoleHelper::FG_GREEN]);
    }

    /**
     * Return daemonize.
     * @return string
     */
    public function getDaemonize()
    {
        return isset($this->set['daemonize'])
            ? $this->consoleString('true', [ConsoleHelper::BOLD, ConsoleHelper::FG_GREEN])
            : $this->consoleString('false', [ConsoleHelper::BOLD, ConsoleHelper::FG_RED]);
    }

    /**
     * Return PHP verison.
     * @return string
     */
    public function getPhpVersion()
    {
        return $this->consoleString(PHP_VERSION, [ConsoleHelper::BOLD, ConsoleHelper::FG_GREEN]);
    }

    /**
     * Return Swoole version.
     * @return string
     */
    public function getSwooleVersion()
    {
        return $this->consoleString(SWOOLE_VERSION, [ConsoleHelper::BOLD, ConsoleHelper::FG_GREEN]);
    }

    /**
     * Console string to cli.
     * @param $string
     * @param  array  $format
     *
     * @return string
     */
    public function consoleString($string, $format = [ConsoleHelper::BOLD, ConsoleHelper::FG_GREY, ConsoleHelper::BG_BLACK])
    {
        return ConsoleHelper::ansiFormat($string, $format);
    }

    /**
     * Swoole event start.
     * @param $http
     */
    public function start($http)
    {
        swoole_set_process_name("eazy {$http->master_pid}");
        $this->welcome();
    }
    
    
}