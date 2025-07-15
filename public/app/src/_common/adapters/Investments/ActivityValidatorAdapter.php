<?php

namespace crm\src\_common\adapters\Investments;

use crm\src\services\Validator;
use crm\src\_common\interfaces\AValidatorAdapter;

/**
 * Валидатор для InvInvActivityInputDto.
 */
class InvInvActivityValidatorAdapter extends AValidatorAdapter
{
    protected function buildValidator(): Validator
    {
        $validator = new Validator();

        // $validator->addRule('leadUid', function ($value) {
        //     if (!is_string($value) || strlen($value) < 3) {
        //         return 'Поле leadUid должно быть строкой не короче 3 символов';
        //     }
        //     return null;
        // });

        $validator->addRule('type', function ($value) {
            if ($value !== null && !in_array($value, ['active', 'closed'], true)) {
                return 'Поле type должно быть "active" или "closed"';
            }
            return null;
        });

        $validator->addRule('pair', function ($value) {
            if ($value !== null && !is_string($value)) {
                return 'Поле pair должно быть строкой';
            }
            return null;
        });

        $validator->addRule('amount', function ($value) {
            if ($value !== null && (!is_numeric($value) || $value < 0)) {
                return 'Поле amount должно быть положительным числом';
            }
            return null;
        });

        return $validator;
    }
}
