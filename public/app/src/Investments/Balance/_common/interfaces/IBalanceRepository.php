<?php

namespace crm\src\Investments\Balance\_common\interfaces;

use crm\src\_common\interfaces\IResultRepository;
use crm\src\Investments\Balance\_common\DTOs\DbInvBalanceDto;

/**
 * Интерфейс репозитория инвестиционного баланса.
 *
 * @extends IResultRepository<DbInvBalanceDto>
 */
interface IBalanceRepository extends IResultRepository
{
    /**
     * Возвращает баланс по leadUid.
     *
     * @param  string $leadUid
     * @return IBalanceResult
     */
    public function getByLeadUid(string $leadUid): IBalanceResult;

    /**
     * Удаляет баланс по leadUid.
     *
     * @param  string $leadUid
     * @return IBalanceResult
     */
    public function deleteByLeadUid(string $leadUid): IBalanceResult;
}
