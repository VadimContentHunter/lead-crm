<?php

namespace crm\src\services\Repositories\QueryBuilder;

use crm\src\services\Repositories\QueryBuilder\Query;
use crm\src\services\Repositories\QueryBuilder\QueryFacade;
use crm\src\services\Repositories\QueryBuilder\interfaces\IQueryContext;
use crm\src\services\Repositories\QueryBuilder\interfaces\IQueryModifiers;
use crm\src\services\Repositories\QueryBuilder\interfaces\ICommandOperations;

class QueryBuilder implements IQueryContext
{
    public function table(string $name): IQueryModifiers&ICommandOperations
    {
        $query = new Query();            // создаём единое хранилище
        $query->setTable($name);         // устанавливаем имя таблицы

        return new QueryFacade($query);
    }
}
