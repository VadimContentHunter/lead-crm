<?php

namespace crm\src\Investments\Status\_common\interfaces;

use crm\src\_common\interfaces\IResultRepository;
use Domain\Investment\DTOs\DbInvStatusDto;

/**
 * Интерфейс репозитория инвестиционных статусов.
 *
 * @extends IResultRepository<DbInvStatusDto>
 */
interface IStatusRepository extends IResultRepository
{
    /**
     * Возвращает статус по его уникальному коду.
     *
     * @param  string $code Например: "work", "lost", "deal"
     * @return IStatusResult
     */
    public function getByCode(string $code): IStatusResult;

    /**
     * Удаляет статус по коду.
     *
     * @param  string $code
     * @return IStatusResult
     */
    public function deleteByCode(string $code): IStatusResult;
}
