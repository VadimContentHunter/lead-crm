<?php

namespace crm\src\_common\adapters;

use crm\src\services\Validator;
use crm\src\_common\interfaces\AValidatorAdapter;

/**
 * Валидатор для статусов (Source).
 */
class SourceValidatorAdapter extends AValidatorAdapter
{
    protected function buildValidator(): Validator
    {
        $validator = new Validator();

        $validator->addRule('title', function ($value) {
            if (!is_string($value) || trim($value) === '') {
                return 'Название источника не может быть пустым';
            }
            if (mb_strlen($value) < 3) {
                return 'Название источника должно быть не менее 3 символов';
            }
            if (!preg_match('/^[a-zA-Zа-яА-Я0-9 _-]+$/u', $value)) {
                return 'Название источника содержит недопустимые символы';
            }
            return null;
        });

        return $validator;
    }
}
