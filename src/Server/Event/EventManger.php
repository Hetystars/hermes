<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: Hety <Hetystars@gmail.com>
 * Date: 16/04/2020
 * Time: 16:38
 */

namespace Hermes\Server\Event;

use Hermes\Core\Helper\PhpHelper;

/**
 * Class EventManger
 * @package Hermes\Server\Event
 */
class EventManger
{

    /**
     * @param $className
     * @return array
     * @throws \ReflectionException
     */
    public function getAllRegisterEvent($className): array
    {
        $allEvent = [];
        $allTaskEvent = PhpHelper::getClassNames($className);
        foreach ($allTaskEvent as $eventClass) {
            $allEvent[$eventClass::EVENT_NAME] = $eventClass;
        }
        return $allEvent;
    }

    /**
     * @param $events
     * @return array
     */
    public function formatEvent($events): array
    {
        $allEvent = [];
        foreach ($events as $eventClass) {
            $allEvent[$eventClass::EVENT_NAME] = $eventClass;
        }
        return $allEvent;
    }
}