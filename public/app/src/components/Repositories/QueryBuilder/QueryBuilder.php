<?php

namespace crm\src\components\Repositories\QueryBuilder;

use app\components\Repositories\QueryBuilder\Query;
use crm\src\components\Repositories\QueryBuilder\QueryFacade;
use crm\src\components\Repositories\QueryBuilder\interfaces\IQueryContext;
use crm\src\components\Repositories\QueryBuilder\interfaces\IQueryModifiers;
use crm\src\components\Repositories\QueryBuilder\interfaces\ICommandOperations;

class QueryBuilder implements IQueryContext
{
    public function table(string $name): IQueryModifiers&ICommandOperations
    {
        $query = new Query();            // создаём единое хранилище
        $query->setTable($name);         // устанавливаем имя таблицы

        return new QueryFacade($query);
    }
}
