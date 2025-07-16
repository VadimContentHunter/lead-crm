<?php

namespace crm\src\Investments\InvSource\_common\interfaces;

use crm\src\_common\interfaces\IResult;
use crm\src\Investments\InvSource\_entities\InvSource;
use crm\src\Investments\InvSource\_common\InvSourceCollection;

/**
 * Результат операций с инвестиционным источником или их коллекцией.
 */
interface IInvSourceResult extends IResult
{
    /**
     * Возвращает сущность источника, если она есть.
     *
     * @return InvSource|null
     */
    public function getInvSource(): ?InvSource;

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
