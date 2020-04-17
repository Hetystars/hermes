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
     * @param array $setting
     * @param string $appPath
     * @return bool
     */
    public function handle(int $severType, array $serverParams, array $serverEvents, array $setting, string $appPath): bool;
}
