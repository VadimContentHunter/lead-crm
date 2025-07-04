<?php

namespace crm\src\_common\adapters;

use crm\src\services\Validator;
use crm\src\_common\interfaces\AValidatorAdapter;

/**
 * Валидатор для депозитов.
 */
class DepositValidatorAdapter extends AValidatorAdapter
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

        // Проверка суммы
        $validator->addRule('sum', function ($value) {
            if (!is_numeric($value)) {
                return 'Сумма депозита должна быть числом';
            }
            if ((float)$value < 0) {
                return 'Сумма депозита не может быть отрицательной';
            }
            return null;
        });

        return $validator;
    }
}
