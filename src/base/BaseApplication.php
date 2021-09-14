<?php


namespace toom1996\base;


use toom1996\helpers\Console;
use toom1996\http\Eazy;
use toom1996\server\http\Server;
use toom1996\server\HttpServer;

class BaseApplication extends Console
{
    /**
     * Parameters of console running.
     * @var array
     */
    protected array $consoleParams;

    /**
     * Parameters of server start.
     * @var array
     */
    protected array $startParams;

    protected array $config = [];

    protected $usageCommands = [
        '--start' => 'Start server.',
        '--start -d ' => 'Start server and used daemonize mode.',
        '--stop' => 'Stop server.',
        '--reload' => 'Reload server.',
    ];
    
    public function __construct()
    {
        $this->consoleParams = $this->getParams();
        $this->getConsoleServer();
        if (!in_array(['start', 'reload', 'stop'], $this->consoleParams)) {
            $this->getUsage();
            exit(0);
        }

        if (isset($this->consoleParams['start'])) {
            if (isset($this->consoleParams['d']) && $this->consoleParams['d'] === true) {
                $this->config[$this->startParams['server']]['setting']['daemonize'] = true;
            }
            $this->createServer($this->config[$this->startParams['server']])->run();
        }elseif(isset($this->consoleParams['reload'])) {

        }elseif (isset($this->consoleParams['stop'])) {
            $this->stopServer($this->config[$this->consoleParams[0]]['setting']['pid_file']);
        }
    }

    /**
     * Return swoole server.
     * @return HttpServer
     */
    public function createServer($config)
    {

        Eazy::setAlias('@eazy', __DIR__);
        return new $config['server']($config);
    }

    /**
     * Get console server param.
     * Usage `php eazy --server=http` or `php eazy http`
     */
    protected function getConsoleServer(): void
    {
        $server = (isset($this->consoleParams['server']) ? $this->consoleParams['server'] : (isset($this->consoleParams[0]) ? $this->consoleParams[0] : ''));
        if (!$server || !isset($this->config[$server])) {
            $allServer = implode('|',array_keys($this->config));
            $this->stdout("Usage: {{$allServer}} [OPTION]");
            $this->stdout("\n");
            exit(0);
        }

        $this->startParams['server'] = $server;
    }

    /**
     * Set Parameters of console running.
     * @return array
     */
    protected function getParams(): array
    {
        $rawParams = [];
        if (isset($_SERVER['argv'])) {
            $rawParams = $_SERVER['argv'];
            array_shift($rawParams);
        }

        $params = [];
        foreach ($rawParams as $param) {
            if (preg_match('/^--([\w-]*\w)(=(.*))?$/', $param, $matches)) {
                $name = $matches[1];
                $params[$name] = isset($matches[3]) ? $matches[3] : true;
            } else {
                $params[] = $param;
            }
        }
        return $params;
    }

    protected function getUsage(): void
    {
        $this->stdout('List of [OPTION]' . PHP_EOL);
        $maxLength = 0;
        foreach ($this->usageCommands as $command => $description) {
            $maxLength = max($maxLength, strlen($command));
        }
        foreach ($this->usageCommands as $command => $description){
            $this->stdout('  '.$command);
            $this->stdout(str_repeat(' ', $maxLength + 4 - strlen($command)));
            $this->stdout(Console::wrapText($description, $maxLength + 4 + 2), Console::BOLD);
            $this->stdout("\n");
        }
    }
}