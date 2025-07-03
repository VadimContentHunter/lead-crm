<?php

namespace crm\src\_common\adapters;

use crm\src\services\Validator;
use crm\src\_common\interfaces\AValidatorAdapter;

/**
 * Валидатор для статусов (Status).
 */
class StatusValidatorAdapter extends AValidatorAdapter
{
    protected function buildValidator(): Validator
    {
        $validator = new Validator();

        $validator->addRule('title', function ($value) {
            if (!is_string($value) || trim($value) === '') {
                return 'Название статуса не может быть пустым';
            }
            if (mb_strlen($value) < 3) {
                return 'Название статуса должно быть не менее 3 символов';
            }
            if (!preg_match('/^[a-zA-Zа-яА-Я0-9 _-]+$/u', $value)) {
                return 'Название статуса содержит недопустимые символы';
            }
            return null;
        });

        return $validator;
    }
}
