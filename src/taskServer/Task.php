<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: Hety <Hetystars@gmail.com>
 * Date: 16/04/2020
 * Time: 15:51
 */

namespace Hermes\TaskServer;

use Redis;

/**
 * Class Task
 * @package Hermes\TaskServer
 */
class Task
{
    /**
     * Coroutine
     */
    public const CO = 'co';

    /**
     * Async
     */
    public const ASYNC = 'async';

    /**
     * @var string
     */
    public const FD = 'fd';

    /**
     * @var
     */
    private $host;

    /**
     * @var
     */
    private $port;

    /**
     * @var Redis
     */
    private static $instance;

    /**
     * Task constructor.
     * @param $host
     * @param $port
     */
    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @return Redis
     */
    private function getInstance()
    {
        if (empty(static::$instance)) {
            $redis = new Redis;
            $redis->connect($this->host, $this->port);
            static::$instance = $redis;
        }
        return static::$instance;
    }

    /**
     * @param string $fd
     * @param array $params
     * @return bool|int
     */
    protected function pushTask(array $params)
    {
        return $this->getInstance()->lpush(static::FD, json_encode($params));
    }


    /**
     * @param string $fd
     * @param string $taskEvent
     * @param string $method
     * @param array $params
     * @return bool|int
     */
    public function async(string $taskEvent, string $method, array $params)
    {
        return $this->pushTask([$taskEvent, $method, $params]);
    }

}