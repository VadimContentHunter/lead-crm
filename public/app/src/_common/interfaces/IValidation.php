<?php

namespace crm\src\_common\interfaces;

use crm\src\_common\interfaces\IValidationResult;

interface IValidation
{
    public function validate(object $dataObj): IValidationResult;

    /**
     * @param array<string,mixed> $dataArray
     */
    public function validateArray(array $dataArray): IValidationResult;
}
