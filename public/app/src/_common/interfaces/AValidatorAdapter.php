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

    public function validate(object $dataObj): IValidationResult
    {
        $validator = $this->buildValidator();

        try {
            $validator->validate($dataObj);
            return ValidationResult::success();
        } catch (InvalidArgumentException $e) {
            return ValidationResult::failure($e->getMessage());
        }
    }

    public function validateArray(array $dataArray): IValidationResult
    {
        $validator = $this->buildValidator();

        try {
            $validator->validateArray($dataArray);
            return ValidationResult::success();
        } catch (InvalidArgumentException $e) {
            return ValidationResult::failure($e->getMessage());
        }
    }
}
