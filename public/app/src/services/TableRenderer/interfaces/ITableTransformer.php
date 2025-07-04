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

    /**
     * Оставляет только нужные столбцы и переименовывает заголовки.
     *
     * @param string[]               $header         Оригинальные
     *                                               заголовки
     * @param array<array<string, mixed>> $rows           Строки
     *                                                    данных
     * @param string[]               $allowedColumns Разрешённые столбцы
     *                                               (оригинальные имена)
     * @param array<string, string>  $renameMap      Переименование заголовков
     *                                               ['old_name' => 'Новый заголовок']
     *
     * @return array{header: string[], rows: array<array<mixed>>}
     */
    public function filterAndRename(array $header, array $rows, array $allowedColumns, array $renameMap): array;
}
