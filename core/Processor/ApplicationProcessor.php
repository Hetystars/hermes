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
     * Handle application processors
     * @param $severType
     * @return bool
     * @throws \ReflectionException
     */
    public function handle($severType): bool
    {
        /**
         * @var $serverObj Server
         */
        $serverObj = HermesApplication::TASK_SERVER_MAP[$severType];
        $this->registerEvent($serverObj, $this->initEvent($severType));
        $serverObj->start();
        return true;
    }

    /**
     * @param Server $server
     * @param $event
     * @return
     */
    public function registerEvent(Server $server, $event): array
    {
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
