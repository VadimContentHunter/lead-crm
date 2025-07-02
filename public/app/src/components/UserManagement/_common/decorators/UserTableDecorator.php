<?php

namespace crm\src\components\UserManagement\_common\decorators;

class UserTableDecorator
{
    /**
     * Добавляет кастомный столбец действий (например, кнопки).
     *
     * @param  string[] $header
     * @param  array<array<mixed>> $rows
     * @param  string $actionLabel Название столбца
     * @return array{header: string[], rows: array<array<mixed>>}
     */
    public function decorateWithActions(array $header, array $rows, string $actionLabel = 'Действия'): array
    {
        $newHeader = [...$header, $actionLabel];
        $newRows = [];

        foreach ($rows as $row) {
            $id = $this->extractId($row);
            $buttons = $this->buildActions($id);

            $newRows[] = [...$row, $buttons];
        }

        return [
            'header' => $newHeader,
            'rows' => $newRows
        ];
    }

    protected function extractId(array $row): mixed
    {
        // Предположим, ID — это первое значение (или найди по ключу)
        return $row[0]['value'] ?? $row[0] ?? null;
    }

    protected function buildActions(mixed $id): string
    {
        return <<<HTML
            <button type="button" class="btn-edit" data-id="{$id}">✏️</button>
            <button type="button" class="btn-delete" data-id="{$id}">🗑️</button>
        HTML;
    }
}
