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
     * @param int $severType
     * @param array $serverParams
     * @param array $serverEvents
     * @return bool
     */
    public function handle(int $severType, array $serverParams, array $serverEvents): bool;
}
