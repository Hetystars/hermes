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
     * @param int $severType
     * @param array $serverParams
     * @param array $serverEvents
     * @param array $setting
     * @return bool
     */
    public function handle(int $severType, array $serverParams = [], array $serverEvents = [], array $setting = []): bool
    {
        /**
         * @var $serverObj Server
         */
        $serverClass = HermesApplication::TASK_SERVER_MAP[$severType];
        $serverObj = new $serverClass();
        $this->registerEvent($serverObj, $serverEvents);
        $serverObj->init(...$serverParams);
        $serverObj->setting($setting);
        $serverObj->start();
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
