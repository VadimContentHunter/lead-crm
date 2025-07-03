<?php

namespace crm\src\_common\interfaces;

use Throwable;

interface IResult
{
    public function isSuccess(): bool;

    public function getInt(): ?int;

    public function getBool(): ?bool;

    public function getData(): mixed;

    public function getArray(): array;

    public function hasNull(): bool;

    public function getError(): ?Throwable;

    /**
     * Применить callable (например, маппер) к текущим данным и вернуть результат.
     *
     * @param  callable $mapper Функция или метод для преобразования данных
     * @return mixed Результат преобразования, или null если данных нет
     */
    public function mapData(callable $mapper): mixed;
}
