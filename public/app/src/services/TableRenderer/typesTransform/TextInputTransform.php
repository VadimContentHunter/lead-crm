<?php

namespace crm\src\services\TableRenderer\typesTransform;

use crm\src\services\TableRenderer\interfaces\ITypeTransform;

class TextInputTransform implements ITypeTransform
{
    /**
     * @var string[]
     */
    protected array $columns;

    public function __construct(array $columns)
    {
        $this->columns = $columns;
    }

    public function getColumnNames(): array
    {
        return $this->columns;
    }

    public function transform(string $column, mixed $value, ?int $row_id = null): mixed
    {
        return [
            'type' => 'text',
            'name' => $column,
            'value' => $value,
            'row_id' => $row_id ?? 0,
        ];
    }
}
