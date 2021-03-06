<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: Hety <Hetystars@gmail.com>
 * Date: 16/04/2020
 * Time: 16:57
 */

namespace Hermes\TaskServer\Contract;

/**
 * Class TaskEvent
 * @package Hermes\TaskServer\Contract
 */
abstract class TaskEvent
{
    /**
     * @var string
     */
    public const EVENT_NAME = '';

    /**
     * [
     * 'method_one'=>'callback_one',
     * 'method_two'=>'callback_two'
     * ]
     * @var array
     */
    public const EVENT_CALLBACK_METHOD_MAP = [];


}