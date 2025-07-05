<?php

namespace crm\src\_common\interfaces;

use InvalidArgumentException;
use crm\src\services\Validator;
use crm\src\_common\interfaces\IValidation;
use crm\src\_common\adapters\ValidationResult;
use crm\src\_common\interfaces\IValidationResult;

abstract class AValidatorAdapter implements IValidation
{
    /**
     * Наследник должен задать нужные правила.
     */
    abstract protected function buildValidator(): Validator;

    public function validate(object $dataObj, array $ignoreFields = []): IValidationResult
    {
        $validator = $this->buildValidator();

        try {
            $validator->validate($dataObj, ignoreFields: $ignoreFields);
            return ValidationResult::success();
        } catch (InvalidArgumentException $e) {
            return ValidationResult::failure($e->getMessage());
        }
    }

    public function validateArray(array $dataArray, array $ignoreFields = []): IValidationResult
    {
        $validator = $this->buildValidator();

        try {
            $validator->validateArray($dataArray, $ignoreFields = []);
            return ValidationResult::success();
        } catch (InvalidArgumentException $e) {
            return ValidationResult::failure($e->getMessage());
        }
    }
}
