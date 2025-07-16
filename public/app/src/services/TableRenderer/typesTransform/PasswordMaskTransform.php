<?php

namespace crm\src\services\TableRenderer\typesTransform;

use crm\src\services\TableRenderer\interfaces\ITypeTransform;

class PasswordMaskTransform implements ITypeTransform
{
    public function getColumnNames(): array
    {
        return ['pass', 'password', 'password_hash'];
    }

    public function transform(string $column, mixed $value, ?int $row_id = null): mixed
    {
        return '**********';
    }
}
