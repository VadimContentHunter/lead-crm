<?php

namespace crm\src\Investments\Deposit\_common\interfaces;

use crm\src\_common\interfaces\IResultRepository;
use crm\src\Investments\Deposit\_common\DTOs\DbInvDepositDto;

/**
 * Интерфейс репозитория депозитов.
 *
 * @extends IResultRepository<DbInvDepositDto>
 */
interface IDepositRepository extends IResultRepository
{
    /**
     * Возвращает все депозиты, связанные с указанным lead UID.
     *
     * @param  string $uid
     * @return IDepositResult
     */
    public function getAllByUid(string $uid): IDepositResult;

    /**
     * Удаляет все депозиты, связанные с указанным lead UID.
     *
     * @param  string $uid
     * @return IDepositResult Содержит массив ID или ошибку
     */
    public function deleteAllByUid(string $uid): IDepositResult;

    /**
     * Возвращает один депозит по его ID.
     *
     * @param  int $id
     * @return IDepositResult
     */
    public function getById(int $id): IDepositResult;
}
