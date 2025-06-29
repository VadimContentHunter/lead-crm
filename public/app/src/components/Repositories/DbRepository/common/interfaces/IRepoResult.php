<?php

namespace crm\src\components\Repositories\DbRepository\common\interfaces;

use Throwable;

/**
 * @template T
 */
interface IRepoResult
{
    public function isSuccess(): bool;

    public function getData(): mixed;

    public function getError(): ?Throwable;

    /**
     * Обработать успешный результат через callback
     *
     * @param callable(T): mixed $handler
     *
     * @throws Throwable
     */
    public function getProcessed(callable $handler): mixed;

    /**
     * @return mixed[]|null
     */
    public function getArrayOrNull(): ?array;

    /**
     * @template U
     * @param    class-string<U> $className
     * @return   U|null
     */
    public function getObjectOrNull(string $className): ?object;
}
