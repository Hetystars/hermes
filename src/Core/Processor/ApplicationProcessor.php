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
     * @param array $serverParams
     * @return bool
     * @throws \ReflectionException
     */
    public function handle($severType, $serverParams = []): bool
    {
        /**
         * @var $serverObj Server
         */
        $serverClass = HermesApplication::TASK_SERVER_MAP[$severType];
        $serverObj = new $serverClass();
        $this->registerEvent($serverObj, $this->initEvent($severType));
        $serverObj->init(...$serverParams);
        $serverObj->start();
        return true;
    }

    /**
     * @param Server $server
     * @param $event
     */
    public function registerEvent(Server $server, $event): void
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
