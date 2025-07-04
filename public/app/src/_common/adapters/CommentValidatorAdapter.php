<?php

namespace crm\src\_common\adapters;

use crm\src\services\Validator;
use crm\src\_common\interfaces\AValidatorAdapter;

/**
 * Валидатор для комментариев.
 */
class CommentValidatorAdapter extends AValidatorAdapter
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

        // Проверка текста комментария
        $validator->addRule('comment', function ($value) {
            if (!is_string($value) || trim($value) === '') {
                return 'Комментарий не может быть пустым';
            }
            if (mb_strlen($value) > 1000) {
                return 'Комментарий не может превышать 1000 символов';
            }
            return null;
        });

        return $validator;
    }
}
