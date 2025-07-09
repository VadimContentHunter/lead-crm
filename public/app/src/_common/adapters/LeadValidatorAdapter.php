<?php

namespace crm\src\_common\adapters;

use crm\src\services\Validator;
use crm\src\_common\interfaces\AValidatorAdapter;

class LeadValidatorAdapter extends AValidatorAdapter
{
    protected function buildValidator(): Validator
    {
        $validator = new Validator();

        $validator->addRule('fullName', function ($value) {
            if (!is_string($value) || trim($value) === '') {
                return 'Полное имя не может быть пустым';
            }
            if (mb_strlen($value) < 3) {
                return 'Полное имя должно содержать не менее 3 символов';
            }
            return null;
        });

        $validator->addRule('contact', function ($value) {
            if (!is_string($value) || trim($value) === '') {
                return 'Контакт не может быть пустым';
            }
            return null;
        });

        $validator->addRule('address', function ($value) {
            if (!is_string($value)) {
                return 'Адрес должен быть строкой';
            }
            return null;
        });

        $validator->addRule('sourceId', function ($value) {
            if ($value !== null && !$this->isIntegerValue($value)) {
                return 'Источник должен быть указан';
            }
            return null;
        });

        // $validator->addRule('source', function ($value) {
        //     if ($value !== null && !is_object($value)) {
        //         return 'Источник должен быть указан';
        //     }
        //     return null;
        // });

        // $validator->addRule('status', function ($value) {
        //     if ($value !== null && !is_object($value)) {
        //         return 'Статус должен быть указан';
        //     }
        //     return null;
        // });

        $validator->addRule('accountManagerId', function ($value) {
            if ($value === null || !$this->isIntegerValue($value) || $value <= 0) {
                return 'Менеджер должен быть указан';
            }
            return null;
        });

        return $validator;
    }

    public function isIntegerValue(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }
}
