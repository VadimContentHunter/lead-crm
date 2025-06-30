<?php

namespace crm\src\services\Repositories\DbRepository\common\interfaces;

interface IRepository
{
    public function executeQuery(IQueryStructure $query): IRepoResult;

    /**
     * @param array<string,mixed> $params
     */
    public function executeSql(string $sql, array $params = []): IRepoResult;
}
