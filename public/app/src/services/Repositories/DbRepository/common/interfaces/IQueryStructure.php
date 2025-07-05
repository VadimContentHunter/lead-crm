<?php

namespace crm\src\services\Repositories\DbRepository\common\interfaces;

/**
 * Интерфейс для объекта, представляющего структуру запроса.
 */
interface IQueryStructure
{
    public function setTable(string $table): void;
    public function getTable(): ?string;

    public function addWhere(string $condition): void;
    /**
     * @return string[]
     */
    public function getWheres(): array;

    public function setOrderBy(string $column, string $direction): void;
    /**
     * @return array<string,string>
     */
    public function getOrderBy(): ?array;

    public function setLimit(int $count): void;
    public function getLimit(): ?int;

    public function setAction(string $action): void;
    public function getAction(): ?string;

    /**
     * @param array<string,mixed> $data
     */
    public function setPayload(array $data): void;
    /**
     * @return array<string,mixed>
     */
    public function getPayload(): array;

    /**
     * @param array<string,mixed> $bindings
     */
    public function setBindings(array $bindings): void;
    /**
     * @return array<string,mixed>
     */
    public function getBindings(): array;
}
