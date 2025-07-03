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
}
