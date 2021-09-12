<?php


namespace toom1996\base;


use toom1996\helpers\Console;

class BaseApplication extends Console
{

    protected $commands = [
        '-start' => 'Start server.',
        '-start -d ' => 'Start server and used daemonize mode.',
        '-stop' => 'Stop server.',
        '-reload' => 'Reload server.',
    ];
    
    public function __construct()
    {
        
    }

    protected function getParams()
    {
        $rawParams = [];
        if (isset($_SERVER['argv'])) {
            $rawParams = $_SERVER['argv'];
            array_shift($rawParams);
        }

        $params = [];
        foreach ($rawParams as $param) {
            if (preg_match('/^-([\w-]*\w)(=(.*))?$/', $param, $matches)) {
                $name = $matches[1];
                $params[$name] = isset($matches[3]) ? $matches[3] : true;
            } else {
                $params[] = $param;
            }
        }
        return $params;
    }
}