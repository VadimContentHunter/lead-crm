<?php

namespace crm\src\Investments\InvBalance\_common\interfaces;

use crm\src\_common\interfaces\IResultRepository;
use crm\src\Investments\InvBalance\_common\DTOs\DbInvBalanceDto;

/**
 * Интерфейс репозитория инвестиционного баланса.
 *
 * @extends IResultRepository<DbInvBalanceDto>
 */
interface IInvBalanceRepository extends IResultRepository
{
    /**
     * Возвращает баланс по leadUid.
     *
     * @param  string $leadUid
     * @return IInvBalanceResult
     */
    public function getByLeadUid(string $leadUid): IInvBalanceResult;

    /**
     * Удаляет баланс по leadUid.
     *
     * @param  string $leadUid
     * @return IInvBalanceResult
     */
    public function deleteByLeadUid(string $leadUid): IInvBalanceResult;
}
