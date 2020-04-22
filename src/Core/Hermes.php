<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: Hety <Hetystars@gmail.com>
 * Date: 16/04/2020
 * Time: 16:59
 */

namespace Hermes\Core;

/**
 * Class Hermes
 * @package Hermes\Core
 */
class Hermes
{
    /**
     * @var string
     */
    public const HERMES_SLOAN = '
  _     _    ________     _________     ___   ____     __    __________   __________
 | |   | |  |  _______|  |  _______|   |  _|  |  |   | | |  | |________|  | ________|
 | |___| |  | |_______   | |_______    | |  | |   | |  | |  | |________|  | |_______
 | |___| |  |  _______|  | |_______|   | |   |     |   | |  |  ________   |_______  |       
 | |   | |  | |_______   | |  | |      | |             | |  | |________|   ______|  |
 |_|   |_|  |_________|  |_|   |_|     |_|             |_|  |__________|  |_________|
 
 '
    ;

    /**
     * @param $setting
     * @return array
     */
    public static function startMsg($setting): array
    {
        return [
            'php' => phpversion(),
            'swoole' => phpversion('swoole'),
            'task server' => $setting['host'] . ':' . $setting['port']
        ];
    }

}