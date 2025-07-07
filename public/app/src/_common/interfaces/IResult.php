<?php

namespace crm\src\_common\interfaces;

use Throwable;

interface IResult
{
    public function isSuccess(): bool;

    public function getInt(): ?int;

    public function getBool(): ?bool;

    public function getData(): mixed;

    /**
     * @return mixed[]
     */
    public function getArray(): array;

    public function hasNull(): bool;

    public function getError(): ?Throwable;

    public function first(): static;

    /**
     * Применить callable (например, маппер) к текущим данным и вернуть результат.
     *
     * @param  callable $mapper Функция или метод для преобразования данных
     * @return mixed Результат преобразования, или null если данных нет
     */
    public function mapData(callable $mapper): mixed;

    /**
     * Применяет маппер к каждому элементу результата и возвращает новый Result с массивом не-null результатов.
     *
     * @template T
     * @param    callable(array<string, mixed>): ?T $mapper
     * @return   static
     */
    public function getValidMappedList(callable $mapper): static;

    /**
     * @template T
     * @param    callable(mixed): (T|null) $mapper
     */
    public function mapEach(callable $mapper, bool $removeNulls = true): static;

    /**
     * Применяет преобразование к данным и возвращает новый экземпляр с преобразованным значением.
     *
     * @template T
     * @param    callable(mixed): T $mapper
     * @return   static
     */
    public function mapToNew(callable $mapper): static;
}
