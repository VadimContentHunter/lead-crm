<?php

namespace crm\src\Investments\InvStatus\_common\interfaces;

use crm\src\_common\interfaces\IResultRepository;
use Domain\Investment\DTOs\DbInvStatusDto;

/**
 * Интерфейс репозитория инвестиционных статусов.
 *
 * @extends IResultRepository<DbInvStatusDto>
 */
interface IInvStatusRepository extends IResultRepository
{
    /**
     * Возвращает статус по его уникальному коду.
     *
     * @param  string $code Например: "work", "lost", "deal"
     * @return IInvStatusResult
     */
    public function getByCode(string $code): IInvStatusResult;

    /**
     * Удаляет статус по коду.
     *
     * @param  string $code
     * @return IInvStatusResult
     */
    public function deleteByCode(string $code): IInvStatusResult;
}
