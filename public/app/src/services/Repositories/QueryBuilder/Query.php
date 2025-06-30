<?php

namespace app\components\Repositories\QueryBuilder;

use crm\src\services\Repositories\DbRepository\common\interfaces\IQueryStructure;

class Query implements IQueryStructure
{
    protected ?string $table = null;
    /**
     * @var string[]
     */
    protected array $wheres = [];
    /**
     * @var array{0:string,1:string}|null
     */
    protected ?array $orderBy = null;
    protected ?int $limit = null;
    protected ?string $action = null;
    /**
     * @var array<string,mixed>
     */
    protected array $payload = [];
    /**
     * @var array<string,mixed>
     */
    protected array $bindings = [];

    public function setTable(string $table): void
    {
        $this->table = $table;
    }
    public function getTable(): ?string
    {
        return $this->table;
    }

    public function addWhere(string $condition): void
    {
        $this->wheres[] = $condition;
    }

    /**
     * @return string[]
     */
    public function getWheres(): array
    {
        return $this->wheres;
    }

    public function setOrderBy(string $column, string $direction): void
    {
        $this->orderBy = [$column, strtoupper($direction)];
    }

    /**
     * @return array{0:string,1:string}|null
     */
    public function getOrderBy(): ?array
    {
        return $this->orderBy;
    }

    public function setLimit(int $count): void
    {
        $this->limit = $count;
    }
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }
    public function getAction(): ?string
    {
        return $this->action;
    }

    /**
     * @param array<string,mixed> $data
     */
    public function setPayload(array $data): void
    {
        $this->payload = $data;
    }

    /**
     * @return array<string,mixed>
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @param array<string,mixed> $bindings
     */
    public function setBindings(array $bindings): IQueryStructure
    {
        $this->bindings = $bindings;

        return $this;
    }

    /**
     * @return array<string,mixed>
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }
}
