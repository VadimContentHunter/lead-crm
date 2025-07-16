<?php

namespace crm\src\services\TableRenderer\interfaces;

interface ITypeTransform
{
    /**
     * @return string[]
     */
    public function getColumnNames(): array;

    public function transform(string $column, mixed $value, ?int $row_id = null): mixed;
}
