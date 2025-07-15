<?php

namespace crm\src\Investments\Source\_common\interfaces;

use crm\src\_common\interfaces\IResultRepository;
use crm\src\Investments\Source\_common\DTOs\DbInvSourceDto;
use crm\src\Investments\Source\_common\interfaces\ISourceResult;

/**
 * Интерфейс репозитория инвестиционных источников.
 *
 * @extends IResultRepository<DbInvSourceDto>
 */
interface ISourceRepository extends IResultRepository
{
    /**
     * Возвращает источник по его уникальному коду.
     *
     * @param  string $code Например: "bybit", "binance",
     *                      "telegram"
     * @return ISourceResult
     */
    public function getByCode(string $code): ISourceResult;

    /**
     * Удаляет источник по коду.
     *
     * @param  string $code
     * @return ISourceResult
     */
    public function deleteByCode(string $code): ISourceResult;
}
