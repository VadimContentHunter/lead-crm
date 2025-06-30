<?php

namespace crm\src\components\StatusManagement\_common\adapters;

use Throwable;
use crm\src\components\StatusManagement\_entities\Status;
use crm\src\components\StatusManagement\_common\interfaces\IStatusResult;

class StatusResult implements IStatusResult
{
    private function __construct(
        private mixed $data,
        private ?Throwable $error
    ) {
    }

    public static function success(mixed $data = null): self
    {
        return new self($data, null);
    }

    public static function failure(Throwable $error): self
    {
        return new self(null, $error);
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

    public function getStatus(): ?Status
    {
        return $this->data instanceof Status ? $this->data : null;
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
