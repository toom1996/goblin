<?php


namespace toom1996\db;



use Swoole\Database\RedisConfig;
use Swoole\Database\RedisPool;
use toom1996\base\Component;
use toom1996\base\Exception;

class Redis extends Component
{
    public $pools;

    public $host;
    public $port;
    public $auth;
    public $db_index;
    public $time_out;
    public $size;

    private static $instance;

    public function init()
    {
        var_dump($this);
        echo 'init';
        if (empty($this->pools)) {
            $this->pools = new RedisPool(
                (new RedisConfig())
                    ->withHost($this->host)
                    ->withPort($this->port)
                    ->withAuth($this->auth)
                    ->withDbIndex($this->db_index)
                    ->withTimeout($this->time_out),
                $this->size
            );
        }
    }

    public static function getInstance($config = null, $poolName = 'default')
    {
        if (empty(self::$instance[$poolName])) {
            if (empty($config)) {
                throw new Exception('redis config empty');
            }
            if (empty($config['size'])) {
                throw new Exception('the size of redis connection pools cannot be empty');
            }
            self::$instance[$poolName] = new static($config);
        }

        return self::$instance[$poolName];
    }

    public function getConnection()
    {
        return $this->pools->get();
    }

    public function close($connection = null)
    {
        $this->pools->put($connection);
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function fill(): void
    {
        $this->pools->fill();
    }
}