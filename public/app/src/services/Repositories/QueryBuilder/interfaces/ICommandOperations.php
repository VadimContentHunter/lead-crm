<?php

namespace crm\src\components\Repositories\QueryBuilder\interfaces;

use crm\src\services\Repositories\DbRepository\common\interfaces\IQueryStructure;

/**
 * Интерфейс для выполнения CRUD-операций с данными.
 */
interface ICommandOperations
{
    public function select(): IQueryStructure;
    /**
     * @param array<string,mixed> $data
     */
    public function insert(array $data): IQueryStructure;
    /**
     * @param array<string,mixed> $data
     */
    public function update(array $data): IQueryStructure;
    public function delete(): IQueryStructure;
}
