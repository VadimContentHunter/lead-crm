<?php

namespace crm\src\Investments\Source\_common\interfaces;

use crm\src\_common\interfaces\IResult;
use crm\src\Investments\Source\_entities\InvSource;
use crm\src\Investments\Source\_common\InvSourceCollection;

/**
 * Результат операций с инвестиционным источником или их коллекцией.
 */
interface ISourceResult extends IResult
{
    /**
     * Возвращает сущность источника, если она есть.
     *
     * @return InvSource|null
     */
    public function getSource(): ?InvSource;

    /**
     * Уникальный идентификатор или код источника.
     *
     * @return string|null
     */
    public function getCode(): ?string;

    /**
     * Человеческое название источника.
     *
     * @return string|null
     */
    public function getLabel(): ?string;

    /**
     * Идентификатор записи в БД.
     *
     * @return int|null
     */
    public function getId(): ?int;
}
