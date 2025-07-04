<?php

namespace crm\src\_common\adapters;

use crm\src\services\Validator;
use crm\src\_common\interfaces\AValidatorAdapter;

/**
 * Валидатор для балансов.
 */
class BalanceValidatorAdapter extends AValidatorAdapter
{
    protected function buildValidator(): Validator
    {
        $validator = new Validator();

        // Проверка leadId
        $validator->addRule('leadId', function ($value) {
            if (!is_int($value) || $value <= 0) {
                return 'Некорректный leadId (должен быть положительным числом)';
            }
            return null;
        });

        // Проверка current
        $validator->addRule('current', function ($value) {
            if (!is_numeric($value)) {
                return 'Поле current должно быть числом';
            }
            return null;
        });

        // Проверка drain
        $validator->addRule('drain', function ($value) {
            if (!is_numeric($value)) {
                return 'Поле drain должно быть числом';
            }
            return null;
        });

        // Проверка potential
        $validator->addRule('potential', function ($value) {
            if (!is_numeric($value)) {
                return 'Поле potential должно быть числом';
            }
            return null;
        });

        return $validator;
    }
}
