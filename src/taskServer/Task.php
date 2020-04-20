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
     * @var Redis
     */
    private static $instance;


    /**
     * @return Redis
     */
    private function getInstance()
    {
        if (empty(static::$instance)) {
            $redis = new Redis;
            $redis->connect(TASK_SERVER_HOST, TASK_SERVER_PORT);
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