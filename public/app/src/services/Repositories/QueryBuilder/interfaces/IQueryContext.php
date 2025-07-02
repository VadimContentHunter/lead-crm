<?php

namespace crm\src\services\Repositories\QueryBuilder\interfaces;

use crm\src\services\Repositories\QueryBuilder\interfaces\IQueryModifiers;
use crm\src\services\Repositories\QueryBuilder\interfaces\ICommandOperations;

/**
 * Интерфейс для задания контекста запроса — источника данных (таблицы).
 */
interface IQueryContext
{
    /**
     * Устанавливает таблицу (источник данных) для построения запроса.
     */
    public function table(string $name): IQueryModifiers&ICommandOperations;
}
