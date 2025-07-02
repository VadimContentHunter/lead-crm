<?php

namespace crm\src\_common\adapters;

use crm\src\services\Validator;
use crm\src\_common\interfaces\AValidatorAdapter;

class UserValidatorAdapter extends AValidatorAdapter
{
    protected function buildValidator(): Validator
    {
        $validator = new Validator();

        $validator->addRule('login', function ($value) {
            if (!is_string($value) || trim($value) === '') {
                return 'Логин не может быть пустым';
            }
            if (strlen($value) < 6) {
                return 'Логин должен быть не менее 6 символов';
            }
            if (!preg_match('/^[a-zA-Z]/', $value)) {
                return 'Логин должен начинаться с буквы';
            }
            return null;
        });

        $validator->addRule('plainPassword', function ($value) {
            if (!is_string($value)) {
                return 'Пароль должен быть строкой';
            }
            if ($value !== '' && strlen($value) < 6) {
                return 'Пароль должен быть не менее 6 символов';
            }
            return null;
        });

        $validator->addRule('confirmPassword', function ($value) {
            if (!is_string($value)) {
                return 'Пароль должен быть строкой';
            }
            if ($value !== '' && strlen($value) < 6) {
                return 'Пароль должен быть не менее 6 символов';
            }
            return null;
        });

        return $validator;
    }
}
