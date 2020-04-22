<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: Hety <Hetystars@gmail.com>
 * Date: 16/04/2020
 * Time: 15:51
 */

namespace Hermes\TaskServer;

use Hermes\TaskServer\Exception\TaskException;
use Hermes\TaskServer\Exception\TaskInvalidParamsException;
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
     * @var Redis
     */
    private static $instance;


    /**
     * @return Redis
     * @throws TaskInvalidParamsException
     * @throws TaskException
     */
    private static function getInstance(): Redis
    {
        if (empty(static::$instance)) {
            $redis = new Redis;
            $config = self::getServerConfig();
            if (!$redis->connect(...$config)) {
                throw new TaskException('task redis server connect failed,please check server_params setting');
            }
            static::$instance = $redis;
        }
        return static::$instance;
    }

    /**
     * @return array
     * @throws TaskInvalidParamsException
     */
    protected static function getServerConfig(): array
    {
        $config = require dirname(__DIR__, 5) . '/hermes_config.php';
        if (empty($config['server_params']['host']) || empty($config['server_params']['port'])) {
            throw new TaskInvalidParamsException('server_params host or port is invalid');
        }
        return [
            $config['server_params']['host'],
            $config['server_params']['port']
        ];
    }

    /**
     * @param array $params
     * @return bool|int
     * @throws TaskInvalidParamsException
     * @throws TaskException
     */
    protected static function pushTask(array $params)
    {
        return self::getInstance()->lpush(static::FD, json_encode($params));
    }


    /**
     * @param string $taskEvent
     * @param string $method
     * @param array $params
     * @return bool|int
     * @throws TaskInvalidParamsException
     * @throws TaskException
     */
    public static function async(string $taskEvent, string $method, array $params)
    {
        return self::pushTask([$taskEvent, $method, $params]);
    }

}