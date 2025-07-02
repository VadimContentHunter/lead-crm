<?php

namespace crm\src\_common\adapters;

use crm\src\_common\interfaces\IValidationResult;

class ValidationResult implements IValidationResult
{
    /**
     * @param string[] $errors
     */
    private function __construct(
        private array $errors = []
    ) {
    }

    public static function success(): self
    {
        return new self([]);
    }

    public static function failure(string ...$errors): self
    {
        return new self($errors);
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
