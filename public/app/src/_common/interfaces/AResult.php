<?php

namespace crm\src\_common\interfaces;

use Throwable;
use crm\src\_common\interfaces\IResult;

abstract class AResult implements IResult
{
    protected function __construct(
        protected mixed $data,
        protected ?Throwable $error
    ) {
    }

    public static function success(mixed $data = null): static
    {
        /**
         * @phpstan-ignore-next-line
         */
        return new static($data, null);
    }

    public static function failure(Throwable $error): static
    {
        /**
         * @phpstan-ignore-next-line
         */
        return new static(null, $error);
    }

    public function isSuccess(): bool
    {
        return $this->error === null;
    }

    public function getError(): ?Throwable
    {
        return $this->error;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getArray(): array
    {
        return is_array($this->data) ? $this->data : [];
    }

    public function getInt(): ?int
    {
        return is_int($this->data) ? $this->data : null;
    }

    public function getBool(): ?bool
    {
        return is_bool($this->data) ? $this->data : null;
    }

    public function hasNull(): bool
    {
        return $this->data === null;
    }

    /**
     * Применить callable (например, маппер) к текущим данным и вернуть результат.
     *
     * @param  callable $mapper Функция или метод для преобразования данных
     * @return mixed Результат преобразования, или null если данных нет
     */
    public function mapData(callable $mapper): mixed
    {
        if ($this->data === null) {
            return null;
        }
        return $mapper($this->data);
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

    /**
     * Применяет маппер к каждому элементу результата и возвращает новый Result с массивом не-null результатов.
     *
     * @template T
     * @param    callable(array<string, mixed>): ?T $mapper
     * @return   static
     */
    public function getValidMappedList(callable $mapper): static
    {
        if (!is_array($this->data)) {
            return static::success([]);
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

        return static::success($result);
    }

    /**
     * Применяет маппер ко всем элементам массива (любого типа) и возвращает новый Result с результатами.
     *
     * @template T
     * @param    callable(mixed): T|null $mapper
     * @param    bool $removeNulls Если true — удаляет все элементы, преобразованные в null
     * @return   static
     */
    public function mapEach(callable $mapper, bool $removeNulls = true): static
    {
        if (!is_array($this->data)) {
            return static::success([]);
        }

        $result = [];

        foreach ($this->data as $element) {
            $mapped = $mapper($element);

            if ($mapped === null && $removeNulls) {
                continue;
            }

            $result[] = $mapped;
        }

        return static::success($result);
    }
}
