<?php

namespace crm\src\services\TableRenderer\interfaces;

/**
 * Interface for transforming raw rows into UI-compatible format.
 */
interface ITableTransformer
{
    /**
     * @param  string[] $header
     * @param  array<array<string|int, mixed>> $rows
     * @return array<array<mixed>>
     */
    public function transform(array $header, array $rows): array;
}
