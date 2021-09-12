<?php


namespace toom1996;

use toom1996\base\BaseApplication;
use toom1996\base\InvalidConfigException;
use toom1996\helpers\Console;
use toom1996\http\Eazy;
use toom1996\http\Goblin;
use toom1996\http\UrlManager;
use toom1996\server\http\Server;
use toom1996\server\HttpServer;

/**
 * This constant defines the framework installation directory.
 */
defined('EAZY_PATH') or define('EAZY_PATH', __DIR__);

/**
 * Class Application
 *
 * @author: TOOM1996
 */
class Application extends BaseApplication
{

    public $params = [];

    protected $config = [];



    public function __construct(array $config)
    {
        $this->config = $config;
        $this->params = $this->getParams();
        if (empty($this->params)) {
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
            if (!isset($this->config[$this->params[0]])) {
                $this->stdout("Cant find server called `{$this->params[0]}`");
                $this->stdout("\n");
                exit;
            }
            if (isset($this->params['start'])) {
                if ($this->params['d'] === true) {
                    echo '-----';
                    $this->config[$this->params[0]]['setting']['daemonize'] = true;
                }
                $this->createServer($this->config[$this->params[0]])->run();
            }elseif(isset($this->params['reload'])) {
                
            }elseif (isset($this->params['stop'])) {
                $this->stopServer($this->config[$this->params[0]]['setting']['pid_file']);
            }
        }
    }


    public function selectCommand()
    {
        switch ($this->params){
            case isset($this->params['start']) === true:

        }

    }

    /**
     * Return swoole server.
     * @return HttpServer
     */
    public function createServer($config)
    {
        Eazy::setAlias('@eazy', __DIR__);
        return new Server($config);
    }

    public function stopServer($pidFile)
    {
        $pid = file_get_contents($pidFile);
        var_dump($pid);
        posix_kill($pid, SIGTERM);
    }

    public function restartServer()
    {

    }
}