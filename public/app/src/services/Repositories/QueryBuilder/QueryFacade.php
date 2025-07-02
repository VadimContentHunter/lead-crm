<?php

namespace crm\src\services\Repositories\QueryBuilder;

use crm\src\services\Repositories\QueryBuilder\QueryModifiers;
use crm\src\services\Repositories\QueryBuilder\CommandOperations;
use crm\src\services\Repositories\QueryBuilder\interfaces\IQueryModifiers;
use crm\src\services\Repositories\QueryBuilder\interfaces\ICommandOperations;
use crm\src\services\Repositories\DbRepository\common\interfaces\IQueryStructure;

class QueryFacade implements IQueryModifiers, ICommandOperations
{
    protected QueryModifiers $modifiers;
    protected CommandOperations $commands;

    public function __construct(protected IQueryStructure $query)
    {
        $this->modifiers = new QueryModifiers($this->query);
        $this->commands = new CommandOperations($this->query);
    }

    /**
     * ======= IQueryModifiers =======
     */
    public function where(string $condition): static
    {
        $this->modifiers->where($condition);
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $this->modifiers->orderBy($column, $direction);
        return $this;
    }

    public function limit(int $count): static
    {
        $this->modifiers->limit($count);
        return $this;
    }

    /**
     * ======= ICommandOperations =======
     */
    public function select(array $data = []): IQueryStructure
    {
        return $this->commands->select($data);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function insert(array $data): IQueryStructure
    {
        return $this->commands->insert($data);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function update(array $data): IQueryStructure
    {
        return $this->commands->update($data);
    }

    public function delete(array $data = []): IQueryStructure
    {
        return $this->commands->delete($data);
    }
}
