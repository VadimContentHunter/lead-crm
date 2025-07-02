<?php

namespace crm\src\components\UserManagement\_common\transformers;

class UserTableTransformer
{
    /**
     * Преобразует массив строк в формат ячеек ввода.
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
                if ($value === null) {
                    $value = '';
                }

                $transformedRow[] = $this->transformCell($columnName, $value);
            }

            $result[] = $transformedRow;
        }

        return $result;
    }

    /**
     * Преобразует ячейку в input-подобный массив или оставляет как есть.
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
            'pass', 'password', 'password_hash' => '**********',
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
