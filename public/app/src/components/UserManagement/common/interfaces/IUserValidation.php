<?php

namespace crm\src\components\UserManagement\common\interfaces;

interface IUserValidation
{
    public function validate(object $dataObj): IValidationResult;

    public function validateArray(array $dataArray): IValidationResult;
}
