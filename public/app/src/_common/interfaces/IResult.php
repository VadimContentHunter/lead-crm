<?php

namespace crm\src\_common\interfaces;

use Throwable;

interface IResult
{
    /**
     * Создаёт успешный результат.
     *
     * @param  mixed $data
     * @return static
     */
    public static function success(mixed $data = null): static;

    /**
     * Создаёт ошибочный результат.
     *
     * @param  Throwable $error
     * @return static
     */
    public static function failure(Throwable $error): static;

    public function isSuccess(): bool;

    public function getInt(): ?int;

    public function getBool(): ?bool;

    public function getData(): mixed;

    public function getString(): ?string;

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
     * @param  callable $mapper
     * @return mixed
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
     * @template T
     * @param    callable(mixed): T $mapper
     * @return   static
     */
    public function mapToNew(callable $mapper): static;
}
