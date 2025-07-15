<?php

namespace crm\src\_common\adapters\Investments;

use crm\src\services\Validator;
use crm\src\_common\interfaces\AValidatorAdapter;

/**
 * Валидатор для InvLeadInputDto.
 */
class InvLeadValidatorAdapter extends AValidatorAdapter
{
    protected function buildValidator(): Validator
    {
        $validator = new Validator();

        $validator->addRule('contact', function ($value) {
            if ($value !== null && (!is_string($value) || trim($value) === '')) {
                return 'Поле contact должно быть непустой строкой';
            }
            return null;
        });

        // $validator->addRule('phone', function ($value) {
        //     if ($value !== null && (!is_string($value) || trim($value) === '')) {
        //         return 'Поле phone должно быть непустой строкой';
        //     }
        //     return null;
        // });

        // $validator->addRule('email', function ($value) {
        //     if ($value !== null && (!is_string($value) || trim($value) === '')) {
        //         return 'Поле email должно быть непустой строкой';
        //     }
        //     return null;
        // });

        // $validator->addRule('fullName', function ($value) {
        //     if ($value !== null && (!is_string($value) || trim($value) === '')) {
        //         return 'Поле fullName должно быть непустой строкой';
        //     }
        //     return null;
        // });

        return $validator;
    }
}
