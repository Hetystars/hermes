<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: Hety <Hetystars@gmail.com>
 * Date: 16/04/2020
 * Time: 15:28
 */

namespace Hermes\TaskServer\Event;

/**
 * Trait TaskEventTrait
 * @package Hermes\TaskServer\Event
 */
Trait TaskEventTrait
{
    /**
     * @var array
     */
    private static $eventMap = [];


    /**
     * @param string $eventName
     * @param $event
     */
    public function registerEvent(string $eventName, $event): void
    {
        if (!isset(static::$eventMap[$eventName])) {
            static::$eventMap[$eventName] = $event;
        }
    }
}