<?php

namespace crm\src\_common\adapters\Investments;

use crm\src\services\Validator;
use crm\src\_common\interfaces\AValidatorAdapter;

/**
 * Валидатор для InvCommentInputDto.
 */
class InvCommentValidatorAdapter extends AValidatorAdapter
{
    protected function buildValidator(): Validator
    {
        $validator = new Validator();

        $validator->addRule('body', function ($value) {
            if (!is_string($value) || strlen(trim($value)) === 0) {
                return 'Поле body не должно быть пустым';
            }
            return null;
        });

        $validator->addRule('who', function ($value) {
            if ($value !== null && !is_string($value)) {
                return 'Поле who должно быть строкой';
            }
            return null;
        });

        $validator->addRule('whoId', function ($value) {
            if ($value !== null && !is_string($value)) {
                return 'Поле whoId должно быть строкой или null';
            }
            return null;
        });

        // $validator->addRule('option', function ($value) {
        //     if (!is_int($value)) {
        //         return 'Поле option должно быть целым числом';
        //     }
        //     return null;
        // });

        return $validator;
    }
}
