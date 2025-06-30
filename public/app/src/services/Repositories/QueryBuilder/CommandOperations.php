<?php

namespace crm\src\components\Repositories\QueryBuilder;

use crm\src\components\Repositories\QueryBuilder\interfaces\ICommandOperations;
use crm\src\services\Repositories\DbRepository\common\interfaces\IQueryStructure;

class CommandOperations implements ICommandOperations
{
    public function __construct(protected IQueryStructure $query)
    {
    }

    public function select(): IQueryStructure
    {
        $this->query->setAction('select');
        return $this->query;
    }

    /**
     * @param array<string,mixed> $data
     */
    public function insert(array $data): IQueryStructure
    {
        $this->query->setAction('insert');
        $this->query->setPayload($data);
        return $this->query;
    }

    /**
     * @param array<string,mixed> $data
     */
    public function update(array $data): IQueryStructure
    {
        $this->query->setAction('update');
        $this->query->setPayload($data);
        return $this->query;
    }

    public function delete(): IQueryStructure
    {
        $this->query->setAction('delete');
        return $this->query;
    }
}
