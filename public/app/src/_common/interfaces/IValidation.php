<?php

namespace crm\src\_common\interfaces;

use crm\src\_common\interfaces\IValidationResult;

interface IValidation
{
    /**
     * @param string[] $ignoreFields
     */
    public function validate(object $dataObj, array $ignoreFields = []): IValidationResult;

    /**
     * @param array<string,mixed> $dataArray
     * @param string[] $ignoreFields
     */
    public function validateArray(array $dataArray, array $ignoreFields = []): IValidationResult;
}
