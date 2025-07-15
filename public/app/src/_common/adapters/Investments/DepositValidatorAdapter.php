<?php

namespace crm\src\_common\adapters\Investments;

use crm\src\services\Validator;
use crm\src\_common\interfaces\AValidatorAdapter;

/**
 * Валидатор для InvDepositInputDto.
 */
class DepositValidatorAdapter extends AValidatorAdapter
{
    protected function buildValidator(): Validator
    {
        $validator = new Validator();

        // $validator->addRule('uid', function ($value) {
        //     if (!is_string($value) || strlen($value) < 3) {
        //         return 'Поле uid должно быть строкой не короче 3 символов';
        //     }
        //     return null;
        // });

        $validator->addRule('sum', function ($value) {
            if ($value !== null && (!is_numeric($value) || $value < 0)) {
                return 'Поле sum должно быть положительным числом';
            }
            return null;
        });

        $validator->addRule('created', function ($value) {
            if ($value !== null && strtotime($value) === false) {
                return 'Поле created должно быть корректной датой/временем';
            }
            return null;
        });

        return $validator;
    }
}
