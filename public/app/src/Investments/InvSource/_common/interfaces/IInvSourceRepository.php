<?php

namespace crm\src\Investments\InvSource\_common\interfaces;

use crm\src\_common\interfaces\IResultRepository;
use crm\src\Investments\InvSource\_common\DTOs\DbInvSourceDto;
use crm\src\Investments\InvSource\_common\interfaces\IInvSourceResult;

/**
 * Интерфейс репозитория инвестиционных источников.
 *
 * @extends IResultRepository<DbInvSourceDto>
 */
interface IInvSourceRepository extends IResultRepository
{
    /**
     * Возвращает источник по его уникальному коду.
     *
     * @param  string $code Например: "bybit", "binance",
     *                      "telegram"
     * @return IInvSourceResult
     */
    public function getByCode(string $code): IInvSourceResult;

    /**
     * Удаляет источник по коду.
     *
     * @param  string $code
     * @return IInvSourceResult
     */
    public function deleteByCode(string $code): IInvSourceResult;
}
