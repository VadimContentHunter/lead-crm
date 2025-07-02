<?php

namespace crm\src\services\Repositories\QueryBuilder\interfaces;

use crm\src\services\Repositories\DbRepository\common\interfaces\IQueryStructure;

/**
 * Интерфейс для выполнения CRUD-операций с данными.
 */
interface ICommandOperations
{
    /**
     * @param array<string,mixed> $data
     */
    public function select(array $data = []): IQueryStructure;
    /**
     * @param array<string,mixed> $data
     */
    public function insert(array $data): IQueryStructure;
    /**
     * @param array<string,mixed> $data
     */
    public function update(array $data): IQueryStructure;
    /**
     * @param array<string,mixed> $data
     */
    public function delete(array $data = []): IQueryStructure;
}
