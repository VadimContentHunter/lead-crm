<?php

namespace crm\src\components\UserManagement\common\interfaces;

interface IUserValidation
{
    public function validate(object $dataObj): void;
}
