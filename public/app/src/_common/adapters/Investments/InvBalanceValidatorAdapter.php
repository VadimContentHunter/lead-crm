<?php

namespace crm\src\_common\adapters\Investments;

use crm\src\services\Validator;
use crm\src\_common\interfaces\AValidatorAdapter;

/**
 * Валидатор для InputInvBalanceDto.
 */
class InvBalanceValidatorAdapter extends AValidatorAdapter
{
    protected function buildValidator(): Validator
    {
        $validator = new Validator();

        $validator->addRule('leadUid', function ($value) {
            if (!is_string($value) || strlen(trim($value)) < 3) {
                return 'Поле leadUid должно быть строкой не короче 3 символов';
            }
            return null;
        });

        $validator->addRule('deposit', function ($value) {
            if (!is_numeric($value) || $value < 0) {
                return 'Поле deposit должно быть неотрицательным числом';
            }
            return null;
        });

        $validator->addRule('potation', function ($value) {
            if (!is_numeric($value) || $value < 0) {
                return 'Поле potation должно быть неотрицательным числом';
            }
            return null;
        });

        return $validator;
    }
}
