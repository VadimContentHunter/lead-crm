<?php

namespace crm\src\_common\adapters\Investments;

use crm\src\services\Validator;
use crm\src\_common\interfaces\AValidatorAdapter;
use crm\src\Investments\InvActivity\_entities\DealType;
use crm\src\Investments\InvActivity\_entities\DealDirection;

/**
 * Валидатор для InvActivityInputDto.
 */
class InvActivityValidatorAdapter extends AValidatorAdapter
{
    protected function buildValidator(): Validator
    {
        $validator = new Validator();

        $validator->addRule('leadUid', function ($value) {
            if (is_string($value) && ctype_digit($value)) {
                $value = (int) $value;
            }

            if (!is_int($value) || $value <= 0) {
                return 'Поле leadUid должно быть положительным числом';
            }

            return null;
        });

        $validator->addRule('type', function ($value) {
            $valid = array_map(fn($d) => $d->value, DealType::cases());

            if ($value !== null && !in_array($value, $valid, true)) {
                return 'Поле type должно быть одним из: ' . implode(', ', $valid);
            }

            return null;
        });

        $validator->addRule('direction', function ($value) {
            $valid = array_map(fn($d) => $d->value, DealDirection::cases());

            if ($value !== null && !in_array($value, $valid, true)) {
                return 'Поле direction должно быть одним из: ' . implode(', ', $valid);
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

        $validator->addRule('result', function ($value) {
            if ($value !== null && (!is_numeric($value) || $value < 0)) {
                return 'Поле amount должно быть положительным числом';
            }
            return null;
        });

        return $validator;
    }
}
