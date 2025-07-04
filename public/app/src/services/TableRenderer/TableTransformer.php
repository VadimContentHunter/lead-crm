<?php

namespace crm\src\services\TableRenderer;

use crm\src\services\TableRenderer\interfaces\ITableTransformer;

class TableTransformer implements ITableTransformer
{
    public function transform(array $header, array $rows): array
    {
        $result = [];

        foreach ($rows as $row) {
            $transformedRow = [];

            foreach ($header as $columnName) {
                $value = $row[$columnName] ?? '';
                $transformedRow[] = $this->transformCell($columnName, $value);
            }

            $result[] = $transformedRow;
        }

        return $result;
    }

    protected function transformCell(string $column, mixed $value): mixed
    {
        if ($value === null) {
            return '';
        }

        return match ($column) {
            // 'username', 'login' => ['type' => 'text', 'name' => $column, 'value' => $value],
            // 'age' => ['type' => 'number', 'name' => $column, 'value' => $value],
            // 'status' => [
            //     'type' => 'select',
            //     'name' => 'status',
            //     'value' => $value,
            //     'options' => ['active' => 'Активен', 'blocked' => 'Заблокирован']
            // ],
            'pass', 'password', 'password_hash' => '**********',
            default => $value,
        };
    }


    public function filterAndRename(array $header, array $rows, array $allowedColumns, array $renameMap): array
    {
        // Переименовываем заголовки
        $filteredHeader = [];
        foreach ($allowedColumns as $column) {
            if (in_array($column, $header, true)) {
                $filteredHeader[] = $renameMap[$column] ?? $column;
            }
        }

        // Проходим по каждому row и заполняем все allowedColumns
        $filteredRows = [];
        foreach ($rows as $row) {
            $filteredRow = [];
            foreach ($allowedColumns as $column) {
                $value = $row[$column] ?? null; // если нет значения — подставляем null
                $filteredRow[] = $this->transformCell($column, $value);
            }
            $filteredRows[] = $filteredRow;
        }

        return [
        'header' => $filteredHeader,
        'rows'   => $filteredRows,
        ];
    }
}
