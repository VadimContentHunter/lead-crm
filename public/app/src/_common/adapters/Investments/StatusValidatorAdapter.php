<?php

namespace crm\src\_common\adapters\Investments;

use crm\src\services\Validator;
use crm\src\_common\interfaces\AValidatorAdapter;

/**
 * Валидатор для InvStatusInputDto.
 */
class StatusValidatorAdapter extends AValidatorAdapter
{
    protected function buildValidator(): Validator
    {
        $validator = new Validator();

        $validator->addRule('code', function ($value) {
            if ($value !== null) {
                if (!is_string($value) || trim($value) === '') {
                    return 'Поле code должно быть непустой строкой';
                }

                if (!preg_match('/^[a-z0-9_]+$/', $value)) {
                    return 'Поле code может содержать только латинские буквы в нижнем регистре, цифры и символ "_"';
                }
            }
            return null;
        });

        $validator->addRule('label', function ($value) {
            if ($value !== null && (!is_string($value) || trim($value) === '')) {
                return 'Поле label должно быть непустой строкой';
            }
            return null;
        });

        return $validator;
    }
}
