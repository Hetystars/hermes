<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: Hety <Hetystars@gmail.com>
 * Date: 16/04/2020
 * Time: 15:04
 */

namespace Hermes\TaskServer;


use Hermes\{
    Core\Helper\PhpHelper,
    Server\Exception\ServerException,
    Server\Server,
    TaskServer\Event\TaskEventTrait,
    TaskServer\Exception\TaskException
};
use Swoole\Redis\Server as RedisServer;

/**
 * Class TaskServer
 * @package Hermes\TaskServer
 */
class TaskServer extends Server
{
    use TaskEventTrait;

    /**
     * Start server
     *
     * @throws ServerException
     */
    public function start(): void
    {
        $this->swooleServer = new RedisServer($this->host, $this->port, $this->mode, $this->type);
        $this->startSwoole();
    }

    /**
     *
     */
    protected function startSwoole(): void
    {
        $this->swooleServer->set($this->getSetting());
        $this->swooleServer->setHandler('LPUSH', function ($fd, $data) {
            $taskId = $this->swooleServer->task($data);
            if ($taskId === false) {
                return RedisServer::format(RedisServer::ERROR);
            }
            return RedisServer::format(RedisServer::INT, $taskId);
        });

        $this->swooleServer->on('Finish', function ($response) {
            $this->taskResponse($response);
        });

        $this->swooleServer->on('Task', function ($serv, $taskId, $workerId, $data) {
            //处理任务
            $params = json_decode($data[1], true);
            $this->handleTask($params[0], $params[1], $params[2]);
        });

        $this->swooleServer->start();
    }

    /**
     * @param string $taskName
     * @param string $taskMethod
     * @param array $params
     * @return mixed
     * @throws TaskException
     */
    protected function handleTask(string $taskName, string $taskMethod, array $params)
    {
        if (!isset(static::$eventMap[$taskName])) {
            throw new TaskException();
        }
        $taskObj = new static::$eventMap[$taskName]();
        if (!method_exists($taskObj, $taskMethod)) {
            throw new TaskException();
        }
        return PhpHelper::call([$taskObj, $taskMethod], $params);
    }


    /**
     * @TODO 待实现
     * @param $response
     */
    protected function taskResponse($response)
    {
        file_put_contents('/tmp/record.log', PHP_EOL . json_encode($response), FILE_APPEND);
    }

    /**
     * @inheritDoc
     */
    public function startWithDaemonize(): void
    {
        // TODO: Implement startWithDaemonize() method.
    }

    /**
     * @inheritDoc
     */
    public function stop(): bool
    {
        // TODO: Implement stop() method.
    }

    /**
     * @inheritDoc
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @inheritDoc
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function getMode(): int
    {
        return $this->mode;
    }

    /**
     * @param $setting
     */
    public function setting($setting): void
    {
        $this->setting = $setting;
    }

    /**
     * @inheritDoc
     */
    public function getSetting(): array
    {
        return [
            'task_worker_num' => 4,
            'worker_num' => 1,
            'log_file' => '/tmp/swoole.log',
            'log_level' => SWOOLE_LOG_NOTICE,
            'daemonize' => 1
        ];
        return $this->setting;
    }

    /**
     * @inheritDoc
     */
    public function getTypeName(): string
    {
        // TODO: Implement getTypeName() method.
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return array
     */
    public function getOn(): array
    {
        return $this->on;
    }

    /**
     * @inheritDoc
     */
    public function getListener(): array
    {
        // TODO: Implement getListener() method.
    }
}