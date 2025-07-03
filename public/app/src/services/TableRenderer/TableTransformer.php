<?php

namespace crm\src\services\TableRenderer;

use crm\src\services\TableRenderer\interfaces\ITableTransformer;

/**
 * Универсальный трансформер, который преобразует значения ячеек в input-подобные элементы.
 */
class TableTransformer implements ITableTransformer
{
    /**
     * Преобразует строки в элементы управления для рендера.
     *
     * @param  string[] $header
     * @param  array<array<string|int, mixed>> $rows
     * @return array<array<mixed>>
     */
    public function transform(array $header, array $rows): array
    {
        $result = [];

        foreach ($rows as $row) {
            $transformedRow = [];

            foreach ($header as $i => $columnName) {
                $value = $row[$i] ?? '';
                $transformedRow[] = $this->transformCell($columnName, $value);
            }

            $result[] = $transformedRow;
        }

        return $result;
    }

    /**
     * Преобразует отдельную ячейку в input или оставляет как есть.
     *
     * @param  string $column
     * @param  mixed $value
     * @return mixed
     */
    protected function transformCell(string $column, mixed $value): mixed
    {
        return match ($column) {
            'username', 'login' => ['type' => 'text', 'name' => $column, 'value' => $value],
            'age' => ['type' => 'number', 'name' => $column, 'value' => $value],
            'status' => [
                'type' => 'select',
                'name' => 'status',
                'value' => $value,
                'options' => ['active' => 'Активен', 'blocked' => 'Заблокирован']
            ],
            default => $value,
        };
    }
}
