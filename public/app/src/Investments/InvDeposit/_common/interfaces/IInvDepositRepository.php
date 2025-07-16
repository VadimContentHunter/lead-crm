<?php

namespace crm\src\Investments\InvDeposit\_common\interfaces;

use crm\src\_common\interfaces\IResultRepository;
use crm\src\Investments\InvDeposit\_common\DTOs\DbInvDepositDto;

/**
 * Интерфейс репозитория депозитов.
 *
 * @extends IResultRepository<DbInvDepositDto>
 */
interface IInvDepositRepository extends IResultRepository
{
    /**
     * Возвращает все депозиты, связанные с указанным lead UID.
     *
     * @param  string $uid
     * @return IInvDepositResult
     */
    public function getAllByUid(string $uid): IInvDepositResult;

    /**
     * Удаляет все депозиты, связанные с указанным lead UID.
     *
     * @param  string $uid
     * @return IInvDepositResult Содержит массив ID или ошибку
     */
    public function deleteAllByUid(string $uid): IInvDepositResult;

    /**
     * Возвращает один депозит по его ID.
     *
     * @param  int $id
     * @return IInvDepositResult
     */
    public function getById(int $id): IInvDepositResult;
}
