<?php

namespace crm\src\services\Repositories\QueryBuilder;

use crm\src\services\Repositories\QueryBuilder\interfaces\ICommandOperations;
use crm\src\services\Repositories\DbRepository\common\interfaces\IQueryStructure;

class CommandOperations implements ICommandOperations
{
    public function __construct(protected IQueryStructure $query)
    {
    }

    /**
     * @param array<string,mixed> $data
     */
    public function select(array $data = []): IQueryStructure
    {
        $this->query->setAction('select');
        $this->query->setPayload($data);
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

    /**
     * @param array<string,mixed> $data
     */
    public function delete(array $data = []): IQueryStructure
    {
        $this->query->setAction('delete');
        $this->query->setPayload($data);
        return $this->query;
    }

    /**
     * @param array<string, mixed> $bindings
     */
    public function bindings(array $bindings): ICommandOperations
    {
        $currentBindings = $this->query->getBindings();
        $this->query->setBindings([...$currentBindings, ...$bindings]);

        return $this;
    }
}
