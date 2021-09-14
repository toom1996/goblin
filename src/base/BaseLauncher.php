<?php

namespace toom1996\base;


use toom1996\helpers\Console;
use toom1996\http\Eazy;
use toom1996\server\http\Server;
use toom1996\server\HttpServer;

/**
 * Class BaseLauncher
 * 
 * @property EazyPid $eazyPid The running server pid.
 *
 * @author: TOOM1996
 * @since 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class BaseLauncher extends Component
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

    /**
     * Swoole config.
     * @var array
     */
    public array $servers = [];

    /**
     * Usage commands.
     */
    protected $usageCommands = [
        '--start' => 'Start server.',
        '--start -d ' => 'Start server and used daemonize mode.',
        '--stop' => 'Stop server.',
        '--reload' => 'Reload server.',
    ];

    public function init()
    {
        $this->consoleParams = $this->getParams();
        $this->getConsoleServer();
        if (!in_array(['start', 'reload', 'stop'], $this->consoleParams)) {
            $this->getUsage();
            exit(0);
        }

        if (isset($this->consoleParams['start'])) {
            if (isset($this->consoleParams['d']) && $this->consoleParams['d'] === true) {
                $this->servers[$this->startParams['server']]['setting']['daemonize'] = true;
            }

            $this->createServer($this->servers[$this->startParams['server']])->run();
        }elseif(isset($this->consoleParams['reload'])) {
            $this->reloadServer();
        }elseif (isset($this->consoleParams['stop'])) {
            $this->stopServer();
        }
    }

    /**
     * Return swoole server.
     * @return HttpServer
     */
    public function createServer($config)
    {
        return new $config['server']($config);
    }

    /**
     * Get console server param.
     * Usage `php eazy --server=http` or `php eazy http`
     */
    protected function getConsoleServer(): void
    {
        $server = (isset($this->consoleParams['server']) ? $this->consoleParams['server'] : (isset($this->consoleParams[0]) ? $this->consoleParams[0] : ''));
        if (!$server || !isset($this->servers[$server])) {
            $allServer = implode('|', array_keys($this->servers));
            Console::stdout("Usage: {{$allServer}} [OPTION]");
            Console::stdout("\n");
            $this->getUsage();
            Console::stdout("\n");
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

    /**
     * Print usage to screen.
     */
    protected function getUsage(): void
    {
        Console::stdout('List of [OPTION]' . PHP_EOL);
        $maxLength = 0;
        foreach ($this->usageCommands as $command => $description) {
            $maxLength = max($maxLength, strlen($command));
        }
        foreach ($this->usageCommands as $command => $description){
            Console::stdout('  '.$command);
            Console::stdout(str_repeat(' ', $maxLength + 4 - strlen($command)));
            Console::stdout(Console::wrapText($description, $maxLength + 4 + 2), Console::BOLD);
            Console::stdout("\n");
        }
    }

    /**
     * Rerurn running server pid.
     * @return int
     */
    protected function getEazyPid(): int
    {
        $pid = file_get_contents($this->servers[$this->startParams['server']]['setting']['pid_file']);
        return intval($pid);
    }

    /**
     * Stop server
     */
    protected function stopServer()
    {
        $this->posixKill($this->eazyPid, SIGKILL);
    }

    /**
     * Reload worker server.
     */
    protected function reloadServer()
    {
        $this->posixKill($this->eazyPid, SIGUSR1);
    }

    /**
     *
     * @param  int  $pid
     * @param  int  $Signal
     *
     * @return bool
     */
    protected function posixKill(int $pid, int $Signal): bool
    {
        return @posix_kill($pid, $Signal);
    }
}