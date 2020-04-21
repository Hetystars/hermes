<?php declare(strict_types=1);

namespace Hermes\Core\Processor;


use Hermes\{
    Core\HermesApplication,
    Server\Event\EventManger,
    Server\Server,
    TaskServer\Contract\TaskEvent
};

/**
 * Application processor
 *
 * @since 2.0
 */
class ApplicationProcessor implements ProcessorInterface
{

    /**
     * @param string $command
     * @param int $severType
     * @param array $serverParams
     * @param array $serverEvents
     * @param array $setting
     * @return bool
     */
    public function handle(string $command, int $severType, array $serverParams = [], array $serverEvents = [], array $setting = []): bool
    {
        /**
         * @var $serverObj Server
         */
        $serverClass = HermesApplication::TASK_SERVER_MAP[$severType];
        $serverObj = new $serverClass();
        switch ($command) {
            case HermesApplication::COMMAND_STOP:
                return $serverObj->stop();
            case HermesApplication::COMMAND_INSTALL:
                return $this->install();
            case HermesApplication::COMMAND_RESTART:
            case HermesApplication::COMMAND_START:
                $this->registerEvent($serverObj, $serverEvents);
                $serverObj->init(...$serverParams);
                $serverObj->setting($setting);
                $serverObj->$command();
        }
        return true;
    }


    /**
     * @return bool
     */
    protected function install(): bool
    {
        $filePath = HERMES_ROOT . '/bin';
        if (!is_dir($filePath)) {
            mkdir($filePath, 0777);
            chmod($filePath, 0777);
        }
        file_put_contents(HERMES_ROOT . '/bin/hermes', file_get_contents(HERMES_ROOT . '/vendor/async-task/Hermes/bin/hermes'));
        $configStr = <<<STR
<?php
return [
       'server_type' => \Hermes\Core\HermesApplication::TASK_SERVER,
       'server_setting' => [
            'task_worker_num' => 4,
            'worker_num' => 1,
            'log_file' => '/tmp/swoole.log',
            'log_level' => SWOOLE_LOG_NOTICE,
            'daemonize' => 1,
            'enable_coroutine' => false,
            'response_file' => '/tmp/response.log'
       ],
       'server_event' => [
            
            ],
       'server_params' => [
           'host'=>'127.0.0.1', 
           'port'=>9501, 
           'type'=>SWOOLE_BASE
       ],
   ];
STR;
        file_put_contents(HERMES_ROOT . '/hermes_config.php', $configStr);
        echo 'install success', PHP_EOL, 'please run php bin/hermes start', PHP_EOL;
        return true;
    }

    /**
     * @param Server $server
     * @param $event
     */
    public function registerEvent(Server $server, $event): void
    {
        $event = (new EventManger())->formatEvent($event);
        foreach ($event as $eventName => $eventValue) {
            $server->registerEvent($eventName, $eventValue);
        }
    }

    /**
     * @param $serverType
     * @return array
     * @throws \ReflectionException
     */
    protected function initEvent($serverType): array
    {
        $eventClassName = '';
        switch ($serverType) {
            case HermesApplication::TASK_SERVER:
                $eventClassName = TaskEvent::class;

        }
        return (new EventManger())->getAllRegisterEvent($eventClassName);
    }

}
