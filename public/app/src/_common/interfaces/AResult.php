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
}
