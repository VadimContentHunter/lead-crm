<?php

namespace crm\src\components\Repositories\DbRepository\common\adapter;

use Throwable;
use crm\src\components\Repositories\DbRepository\common\interfaces\IRepoResult;

class RepoResult implements IRepoResult
{
    private function __construct(
        private mixed $data = null,
        private ?Throwable $error = null
    ) {
    }

    public static function success(mixed $data = null): self
    {
        return new self($data);
    }

    public static function failure(Throwable $error): self
    {
        return new self(null, $error);
    }

    public function isSuccess(): bool
    {
        return $this->error === null;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getError(): ?Throwable
    {
        return $this->error;
    }

    /**
     * Обрабатывает успешный результат через callback
     *
     * @param  callable $handler
     * @return mixed
     * @throws Throwable
     */
    public function getProcessed(callable $handler): mixed
    {
        if ($this->isSuccess()) {
            return $handler($this->data);
        }

        throw $this->error;
    }

    /**
     * @return mixed[]|null
     */
    public function getArrayOrNull(): ?array
    {
        return is_array($this->data) ? $this->data : null;
    }

    /**
     * @template T
     * @param    class-string<T> $className Класс, к которому должен принадлежать объект
     * @return   T|null                     Объект этого класса или null
     */
    public function getObjectOrNull(string $className): ?object
    {
        return is_object($this->data) && $this->data instanceof $className
            ? $this->data
            : null;
    }
}
