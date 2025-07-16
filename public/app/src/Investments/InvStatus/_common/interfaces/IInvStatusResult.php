<?php

namespace crm\src\Investments\InvStatus\_common\interfaces;

use crm\src\_common\interfaces\IResult;
use crm\src\Investments\InvStatus\_entities\InvStatus;

/**
 * Результат операций со статусом инвестиции или их коллекцией.
 */
interface IInvStatusResult extends IResult
{
    /**
     * Возвращает сущность статуса, если она есть.
     *
     * @return InvStatus|null
     */
    public function getInvStatus(): ?InvStatus;

    /**
     * Уникальный код статуса (например, work, lost).
     *
     * @return string|null
     */
    public function getCode(): ?string;

    /**
     * Человеческое название статуса.
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
