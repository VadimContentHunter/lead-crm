<?php

namespace crm\src\services\Repositories\DbRepository\common\interfaces;

use Throwable;

interface IRepoResult
{
    public function isSuccess(): bool;

    public function getData(): mixed;

    public function getError(): ?Throwable;

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
}
