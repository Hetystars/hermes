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
    Core\Helper\SystemHelper,
    Core\Hermes,
    Server\Exception\ServerException,
    Server\Server,
    TaskServer\Event\TaskEventTrait,
    TaskServer\Exception\TaskException,
    TaskServer\Exception\TaskInvalidParamsException
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
     * @var string
     */
    public const
        TASK_PROCESS_TITLE = 'hermes task process',
        MANAGER_PROCESS_TITLE = 'hermes manager process';


    /**
     * Start server
     *
     * @throws ServerException
     */
    public function start(): void
    {
        if ($this->checkServerRunningStatus()) {
            $this->swooleServer = new RedisServer($this->host, $this->port, SWOOLE_BASE);
            $this->printMsg()
                ->startSwoole();
        }
    }

    /**
     *
     */
    protected function startSwoole(): void
    {
        $this->swooleServer->set($this->setting);
        $this->swooleServer->setHandler('LPUSH', function ($fd, $data) {
            $taskId = $this->swooleServer->task($data);
            if ($taskId === false) {
                $taskId = RedisServer::format(RedisServer::ERROR);
            } else {
                $taskId = RedisServer::format(RedisServer::INT, $taskId);
            }
            $params = json_decode($data[1], true);
            $this->recoredTaskLog((string)$taskId, $params[0], $params[1], false);
            return (string)$taskId;
        });

        $this->swooleServer->on('Finish', function ($server, $taskId, $response) {
            $this->taskResponse((string)$taskId, $response[0], $response[1], $response[2]);
        });

        $this->swooleServer->on('Task', function ($server, $taskId, $workerId, $data) {
            //处理任务
            $params = json_decode($data[1], true);
            $response = $this->handleTask($params[0], $params[1], $params[2]);
            return [$params[0], $params[1], $response];
        });

        SystemHelper::setProcessTitle(static::TASK_PROCESS_TITLE);

        $this->swooleServer->on('managerStart', function ($server) {
            $this->setPid($server->manager_pid, $server->worker_pid);
            SystemHelper::setProcessTitle(static::MANAGER_PROCESS_TITLE);
        });
        $this->swooleServer->start();
    }

    /**
     * @param string $taskEvent
     * @param string $taskMethod
     * @param array $params
     * @return mixed
     * @throws TaskException
     */
    protected function handleTask(string $taskEvent, string $taskMethod, array $params)
    {
        if (!isset(static::$eventMap[$taskEvent])) {
            throw new TaskException();
        }
        $taskObj = new static::$eventMap[$taskEvent]();
        if (!method_exists($taskObj, $taskMethod)) {
            throw new TaskException();
        }
        return PhpHelper::call([$taskObj, $taskMethod], ...$params);
    }


    /**
     * @param string $taskId
     * @param string $taskEvent
     * @param string $taskMethod
     * @param array $response
     * @return mixed
     * @throws TaskException
     * @throws TaskInvalidParamsException
     */
    protected function taskResponse(string $taskId, string $taskEvent, string $taskMethod, $response)
    {
        if (!isset(static::$eventMap[$taskEvent])) {
            throw new TaskException();
        }
        $this->recoredTaskLog($taskId, $taskEvent, $taskMethod, true);
        $taskEventObj = static::$eventMap[$taskEvent];
        $taskEventCallbackMethodMap = $taskEventObj::EVENT_CALLBACK_METHOD_MAP;
        if (!empty($taskEventCallbackMethodMap)) {
            if (isset($taskEventCallbackMethodMap[$taskMethod])) {
                $taskObj = new $taskEventObj();
                $callBackMethod = $taskEventCallbackMethodMap[$taskMethod];
                if (method_exists($taskObj, $callBackMethod)) {
                    return PhpHelper::call([$taskObj, $callBackMethod], $response);
                }
            }
        }
        return true;
    }

    /**
     * @param string $taskId
     * @param string $taskEvent
     * @param string $taskMethod
     * @param bool $isHandleDone
     * @throws TaskInvalidParamsException
     */
    protected function recoredTaskLog(string $taskId, string $taskEvent, string $taskMethod, $isHandleDone = false)
    {
        $config = require dirname(__DIR__, 5) . '/hermes_config.php';
        if (empty($config['server_setting'])) {
            throw new TaskInvalidParamsException('server_params server_setting is invalid');
        }
        $handMsg = $isHandleDone ? ' handle done' : ' start handle';
        file_put_contents(PhpHelper::formatLogFileWithDate($config['server_setting']['response_file'] ?? static::RESPONSE_LOG), date('Y-m-d H:i:s') . $handMsg . '  task_id:' . (int)$taskId . ' task_event: ' . $taskEvent . ' task_method: ' . $taskMethod . PHP_EOL, FILE_APPEND);
    }

    /**
     * @inheritDoc
     */
    public function startWithDaemonize(): void
    {
        // TODO: Implement startWithDaemonize() method.
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return int
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
        $setting = array_merge($this->getDefaultSetting(), $setting);
        $setting['log_file'] = PhpHelper::formatLogFileWithDate($setting['log_file']);
        $setting['response_file'] = PhpHelper::formatLogFileWithDate($setting['response_file']);
        $this->setting = array_merge($this->getDefaultSetting(), $setting);
    }

    /**
     * @return array
     */
    public function getSetting(): array
    {
        return $this->setting;
    }

    /**
     * @return array
     */
    public function getDefaultSetting(): array
    {
        return [
            'task_worker_num' => 4,
            'worker_num' => 1,
            'log_file' => '/tmp/log/swoole.log',
            'log_level' => SWOOLE_LOG_NOTICE,
            'daemonize' => 1,
            'enable_coroutine' => false,
            'response_file' => '/tmp/log/response.log'
        ];
    }

    /**
     * @return string
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
     * @return array
     */
    public function getListener(): array
    {
        // TODO: Implement getListener() method.
    }

    /**
     * @return bool
     * @throws ServerException
     */
    public function restart(): bool
    {
        $this->stop();
        $this->start();
        return true;
    }

    /**
     * @return $this
     */
    private function printMsg(): self
    {
        PhpHelper::printOut(array_merge([Hermes::HERMES_SLOAN], [
            'php' => phpversion(),
            'swoole' => phpversion('swoole'),
            'task server' => "\033[31m" . $this->host . ':' . $this->port . " \033[0m",
            'task num' => $this->setting['task_worker_num'],
            'swoole_log' => $this->setting['log_file'],
            'response_log' => $this->setting['response_file'],
        ]));
        return $this;
    }

    /**
     * @return bool
     */
    private function checkServerRunningStatus(): bool
    {
        $pid = $this->getPid();
        $managerPid = (int)$pid[0];
        if ($this->isRunning($managerPid)) {
            PhpHelper::printOut(["\033[31m" . 'task server is already running' . " \033[0m"]);
            return false;
        }
        return true;
    }

    /**
     * Get task global unique id
     *
     * @param int $taskId
     *
     * @return string
     */
    protected function getUniqId($taskId): string
    {
        $str = uniqid((string)$taskId, true);
        $strArray = explode('.', $str);
        return $strArray[0] ?? (string)$taskId;
    }
}