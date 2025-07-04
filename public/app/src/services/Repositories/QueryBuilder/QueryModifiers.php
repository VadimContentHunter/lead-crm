<?php

namespace crm\src\services\Repositories\QueryBuilder;

use crm\src\services\Repositories\QueryBuilder\interfaces\IQueryModifiers;
use crm\src\services\Repositories\DbRepository\common\interfaces\IQueryStructure;

class QueryModifiers implements IQueryModifiers
{
    public function __construct(protected IQueryStructure $query)
    {
    }

    public function where(string $condition): static
    {
        $this->query->addWhere($condition);
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $this->query->setOrderBy($column, $direction);
        return $this;
    }

    public function limit(int $count): static
    {
        $this->query->setLimit($count);
        return $this;
    }
}
