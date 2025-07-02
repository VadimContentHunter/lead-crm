<?php

namespace crm\src\services\Repositories\QueryBuilder\interfaces;

/**
 * Интерфейс для задания критериев и модификаторов запроса:
 * фильтрации, сортировки и ограничения количества записей.
 */
interface IQueryModifiers
{
    public function where(string $condition): static;
    public function orderBy(string $column, string $direction = 'ASC'): static;
    public function limit(int $count): static;
}
