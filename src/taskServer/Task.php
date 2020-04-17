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
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * Coroutine
     */
    public const CO = 'co';

    /**
     * Async
     */
    public const ASYNC = 'async';

    /**
     * @var Redis
     */
    private static $instance;

    /**
     * Task constructor.
     * @param string $host
     * @param int $port
     */
    public function __construct(string $host, int $port)
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
    protected function pushTask(string $fd, array $params)
    {
        return $this->getInstance()->lpush($fd, json_encode($params));
    }


    /**
     * @param string $fd
     * @param string $taskEvent
     * @param string $method
     * @param array $params
     * @return bool|int
     */
    public function async(string $fd, string $taskEvent, string $method, array $params)
    {
        return $this->pushTask($fd, [$taskEvent, $method, $params]);
    }

}