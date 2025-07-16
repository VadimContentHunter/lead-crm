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
            $row_id = $row['id'] ?? 0;

            foreach ($header as $columnName) {
                $value = $row[$columnName] ?? '';
                $transformedRow[] = $this->transformCell($columnName, $value, $row_id);
            }

            $result[] = $transformedRow;
        }

        return $result;
    }

    protected function transformCell(string $column, mixed $value, ?int $row_id = null): mixed
    {
        if ($value === null) {
            return '';
        }

        return match ($column) {
            'full_name', 'login', 'title' => ['type' => 'text', 'name' => $column, 'value' => $value, 'row_id' => $row_id ?? 0],
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
            $row_id = $row['id'] ?? 0;
            foreach ($allowedColumns as $column) {
                $value = $row[$column] ?? null; // если нет значения — подставляем null
                $filteredRow[] = $this->transformCell($column, $value, $row_id);
            }
            $filteredRows[] = $filteredRow;
        }

        return [
        'header' => $filteredHeader,
        'rows'   => $filteredRows,
        ];
    }
}
