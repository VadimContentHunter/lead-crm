<?php

namespace crm\src\components\Repositories\DbRepository\common\interfaces;

interface IRepository
{
    public function executeQuery(IQueryStructure $query): IRepoResult;

    /**
     * @param array<string,mixed> $params
     */
    public function executeSql(string $sql, array $params = []): IRepoResult;
}
