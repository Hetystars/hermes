<?php declare(strict_types=1);

namespace Hermes\Core\Processor;

/**
 * Processor interface
 *
 * @since 2.0
 */
interface ProcessorInterface
{
    /**
     * Handle processor
     *
     * Return `true` is to continue
     * @param string $command
     * @param int $severType
     * @param array $serverParams
     * @param array $serverEvents
     * @param array $setting
     * @return bool
     */
    public function handle(string $command, int $severType, array $serverParams, array $serverEvents, array $setting): bool;
}
