<?php

namespace crm\src\services\Repositories\DbRepository\common\adapter;

use Throwable;
use RuntimeException;
use crm\src\services\Repositories\DbRepository\common\interfaces\IRepoResult;

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

    public function getInt(): ?int
    {
        return is_int($this->data) ? $this->data : null;
    }

    /**
     * @param mixed[]|null $trueValues
     * @param mixed[]|null $falseValues
     */
    public function getBool(?array $trueValues = null, ?array $falseValues = null): ?bool
    {
        if ($trueValues !== null || $falseValues !== null) {
            if (is_array($trueValues) && in_array($this->data, $trueValues, true)) {
                return true;
            }

            if (is_array($falseValues) && in_array($this->data, $falseValues, true)) {
                return false;
            }
        }
        return is_bool($this->data) ? $this->data : null;
    }

    public function hasNull(): bool
    {
        return $this->data === null;
    }

    public function isEmpty(): bool
    {
        return empty($this->data) && empty($this->error);
    }

    /**
     * Обрабатывает успешный результат через callback
     *
     * @param  callable $handler
     * @return mixed
     */
    public function getProcessed(callable $handler): mixed
    {
        if ($this->isSuccess()) {
            return $handler($this->data);
        }

        if ($this->error instanceof Throwable) {
            throw $this->error;
        }

        throw new RuntimeException('Неизвестная ошибка');
    }


    /**
     * @return mixed[]|null
     */
    public function getArrayOrNull(): ?array
    {
        return is_array($this->data) ? $this->data : null;
    }

    /**
     * @template T of object
     * @param    class-string<T> $className Класс, к которому должен принадлежать объект
     * @return   T|null Объект этого класса или null
     */
    public function getObjectOrNull(string $className): ?object
    {
        if (is_object($this->data) && $this->data instanceof $className) {
            return $this->data;
        }

        if (is_array($this->data)) {
            try {
                return new $className(...$this->data);
            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * @template T of object
     * @param    class-string<T> $className
     * @param    (callable(array<string, mixed>): T)|null $mapper
     * @return   T|null
     */
    public function getObjectOrNullWithMapper(string $className, ?callable $mapper = null): ?object
    {
        if (is_object($this->data) && $this->data instanceof $className) {
            return $this->data;
        }

        if (is_array($this->data) && $mapper !== null) {
            try {
                return $mapper($this->data);
            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * @template T of object
     * @param    class-string<T> $className
     * @param    callable(array<string, mixed>): T $hydrator
     * @return   T[]
     * @throws   \RuntimeException
     */
    public function getObjectListOrFail(string $className, callable $hydrator): array
    {
        $data = $this->getData();

        if (!is_array($data)) {
            throw new \RuntimeException('Expected array for object list conversion, got ' . gettype($data));
        }

        $result = [];

        foreach ($data as $index => $item) {
            if (!is_array($item)) {
                throw new \RuntimeException("Expected array at index {$index} to hydrate {$className}, got " . gettype($item));
            }

            $object = $hydrator($item);

            if (!is_object($object) || !$object instanceof $className) {
                throw new \RuntimeException("Hydrator did not return instance of {$className} at index {$index}");
            }

            $result[] = $object;
        }

        return $result;
    }

    /**
     * Применяет маппер к каждому элементу результата и возвращает только не-null результаты.
     *
     * @template T
     * @param    callable(array<string, mixed>): ?T $mapper
     * @return   T[]
     */
    public function getValidMappedList(callable $mapper): array
    {
        if (!is_array($this->data)) {
            return [];
        }

        $result = [];

        foreach ($this->data as $row) {
            if (!is_array($row)) {
                continue;
            }

            $mapped = $mapper($row);
            if ($mapped !== null) {
                $result[] = $mapped;
            }
        }

        return $result;
    }

    /**
     * Если data — массив, извлекает из него первый элемент и сохраняет его в data.
     * Возвращает self для цепочек вызовов.
     *
     * @return static
     */
    public function first(): static
    {
        if (is_array($this->data) && !empty($this->data)) {
            $this->data = reset($this->data);
        }

        return $this;
    }
}
