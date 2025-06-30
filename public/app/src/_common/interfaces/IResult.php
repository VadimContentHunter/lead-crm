<?php

namespace crm\src\_common\interfaces;

use Throwable;

interface IResult
{
    public function isSuccess(): bool;

    public function getInt(): ?int;

    public function getBool(): ?bool;

    public function getData(): mixed;

    public function hasNull(): bool;

    public function getError(): ?Throwable;

    /**
     * Применить маппер к текущим данным и вернуть новые данные
     *
     * @param  IMapper $mapper
     * @return object|null
     */
    public function mapData(IMapper $mapper): ?object;
}
