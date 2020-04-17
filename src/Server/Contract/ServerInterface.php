<?php declare(strict_types=1);


namespace Hermes\Server\Contract;

/**
 * Interface ServerInterface
 * @package Hermes\Server\Contract
 */
interface ServerInterface
{
    // Swoole mode list
    public const MODE_LIST = [
        SWOOLE_BASE => 'Base',
        SWOOLE_PROCESS => 'Process',
    ];

    // Swoole socket type list
    public const TYPE_LIST = [
        // SWOOLE_SOCK_TCP | SWOOLE_SSL = 513
        513 => 'TCP & SSL',
        // SWOOLE_SOCK_TCP6 | SWOOLE_SSL = 515
        515 => 'TCP6 & SSL',
        // Normal
        SWOOLE_SOCK_TCP => 'TCP',
        SWOOLE_SOCK_TCP6 => 'TCP6',
        SWOOLE_SOCK_UDP => 'UDP',
        SWOOLE_SOCK_UDP6 => 'UDP6',
        SWOOLE_SOCK_UNIX_DGRAM => 'UNIX DGRAM',
        SWOOLE_SOCK_UNIX_STREAM => 'UNIX STREAM',
    ];

    /**
     * Start swoole server
     *
     * @return void
     */
    public function start(): void;

    /**
     * Stop server
     *
     * @return bool
     */
    public function stop(): bool;


    /**
     * @return string
     */
    public function getHost(): string;

    /**
     * @return int
     */
    public function getPort(): int;

    /**
     * @return int
     */
    public function getMode(): int;

    /**
     * @return array
     */
    public function getSetting(): array;

    /**
     * @return array
     */
    public function getOn(): array;

    /**
     * @return array
     */
    public function getListener(): array;

    /**
     * @param string $eventName
     * @param $event
     * @return mixed
     */
    public function registerEvent(string $eventName, $event);

    /**
     * @return mixed
     */
    public function shutdown();

}