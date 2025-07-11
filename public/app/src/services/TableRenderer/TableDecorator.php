<?php

namespace crm\src\services\TableRenderer;

/**
 * Добавляет дополнительные колонки (например, действия).
 */
class TableDecorator
{
    /**
     * Добавляет в таблицу колонку с действиями.
     *
     * @param  string[] $header
     * @param  array<array<mixed>> $rows
     * @param  string $actionLabel
     * @return array{header: string[], rows: array<array<mixed>>}
     */
    public function decorateWithActions(array $header, array $rows, string $hrefButton = '', string $hrefButtonDel = '', string $actionLabel = ''): array
    {
        $newHeader = [...$header, $actionLabel];
        $newRows = [];

        foreach ($rows as $row) {
            $id = $this->extractId($row);
            $buttons = $this->buildActions($id, $hrefButton, $hrefButtonDel);

            $newRows[] = [...$row, $buttons];
        }

        return [
            'header' => $newHeader,
            'rows' => $newRows
        ];
    }

    /**
     * Извлекает ID из первой колонки.
     *
     * @param array<int,mixed> $row
     */
    protected function extractId(array $row): mixed
    {
        return $row[0]['value'] ?? $row[0] ?? null;
    }

    /**
     * Генерирует HTML-кнопки для строки.
     */
    protected function buildActions(mixed $id, string $href = '', string $hrefDel = ''): string
    {
        return <<<HTML
            <input type="hidden" name="row_id" value="{$id}">
            <a href="{$href}/{$id}" class="btn-icon" data-id="{$id}"><i class="fa-solid fa-pen"></i></a>
            <button type="button" class="btn-icon button-active" data-id="{$id}" href="{$hrefDel}/{$id}"><i class="fa-solid fa-trash"></i></button>
        HTML;
    }
}
