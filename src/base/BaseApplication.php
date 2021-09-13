<?php


namespace toom1996\base;


use toom1996\helpers\Console;

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
        '--server=[server] --start' => 'Start server.',
        '--server=[server] --start -d ' => 'Start server and used daemonize mode.',
        '--server=[server] --stop' => 'Stop server.',
        '--server=[server] --reload' => 'Reload server.',
    ];
    
    public function __construct()
    {
        $this->consoleParams = $this->getParams();
        $this->getConsoleServer();
        if (!in_array(['start', 'reload', 'stop'], $this->consoleParams)) {
            $this->stdout('List of [OPTION]' . PHP_EOL);
            exit(0);
        }

        if (empty($this->consoleParams)) {
            $this->stdout('Usage: [SERVER] [OPTION]...' . PHP_EOL);
            $this->stdout('List of [OPTION]' . PHP_EOL);
            $maxLength = 0;
            foreach ($this->commands as $command => $description) {
                $maxLength = max($maxLength, strlen($command));
            }
            foreach ($this->commands as $command => $description){
                $this->stdout('  '.$command);
                $this->stdout(str_repeat(' ', $maxLength + 4 - strlen($command)));
                $this->stdout(Console::wrapText($description, $maxLength + 4 + 2), Console::BOLD);
                $this->stdout("\n");
            }
        }else{
            if (!isset($this->config[$this->consoleParams[0]])) {
                $this->stdout("Cant find server called `{$this->consoleParams[0]}`");
                $this->stdout("\n");
                exit;
            }
            if (isset($this->consoleParams['start'])) {
                if ($this->consoleParams['d'] === true) {
                    echo '-----';
                    $this->config[$this->consoleParams[0]]['setting']['daemonize'] = true;
                }
                $this->createServer($this->config[$this->consoleParams[0]])->run();
            }elseif(isset($this->consoleParams['reload'])) {

            }elseif (isset($this->consoleParams['stop'])) {
                $this->stopServer($this->config[$this->consoleParams[0]]['setting']['pid_file']);
            }
        }
    }

    /**
     * Get console server param.
     * Usage `php eazy --server=http` or `php eazy http`
     */
    protected function getConsoleServer(): void
    {
        $server = (isset($this->consoleParams['server']) ? $this->consoleParams['server'] : (isset($this->consoleParams[0]) ? $this->consoleParams[0] : ''));
        if (!$server || !isset($this->config[$server])) {
            $this->stdout("Cant find server called `{$server}`");
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

    protected function getUsage(): ?int
    {
        
    }
}