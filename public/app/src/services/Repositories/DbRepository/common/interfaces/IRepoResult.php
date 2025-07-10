<?php

namespace crm\src\services\Repositories\DbRepository\common\interfaces;

use Throwable;

interface IRepoResult
{
    public function isSuccess(): bool;

    public function getData(): mixed;

    public function getError(): ?Throwable;

    public function getInt(): ?int;

    /**
     * @param mixed[]|null $trueValues
     * @param mixed[]|null $falseValues
     */
    public function getBool(?array $trueValues = null, ?array $falseValues = null): ?bool;

    public function hasNull(): bool;

    /**
     * Обработать успешный результат через callback
     *
     * @template T
     * @param    callable(T): mixed $handler
     * @return   mixed
     */
    public function getProcessed(callable $handler): mixed;

    /**
     * @return mixed[]|null
     */
    public function getArrayOrNull(): ?array;

    /**
     * Преобразовать результат в объект указанного класса.
     *
     * @template T of object
     * @param    class-string<T> $className
     * @return   T|null
     */
    public function getObjectOrNull(string $className): ?object;

    /**
     * @template T of object
     * @param    class-string<T> $className Класс, к которому должен принадлежать объект
     * @param    (callable(array<string,mixed>): T)|null $mapper    Кастомный маппер
     * @return   T|null
     */
    public function getObjectOrNullWithMapper(string $className, ?callable $mapper = null): ?object;



    /**
     * @template T of object
     * @param    class-string<T> $className
     * @param    callable(array<string,mixed>): T $hydrator
     * @return   T[]
     * @throws   \RuntimeException
     */
    public function getObjectListOrFail(string $className, callable $hydrator): array;


    /**
     * Применяет маппер к каждому элементу результата и возвращает только не-null результаты.
     *
     * @template T
     * @param    callable(array<string,mixed>): ?T $mapper
     * @return   T[]
     */
    public function getValidMappedList(callable $mapper): array;

     /**
      * Если data — массив, извлекает из него первый элемент и сохраняет его в data.
      * Возвращает self для цепочек вызовов.
      *
      * @return static
      */
    public function first(): static;
}
