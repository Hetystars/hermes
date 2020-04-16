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
     * @param $severType
     * @param $serverParams
     * @return bool
     */
    public function handle($severType,$serverParams): bool;
}
