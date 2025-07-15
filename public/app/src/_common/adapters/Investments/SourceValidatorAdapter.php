<?php

namespace crm\src\_common\adapters\Investments;

use crm\src\services\Validator;
use crm\src\_common\interfaces\AValidatorAdapter;

/**
 * Валидатор для InvSourceInputDto.
 */
class SourceValidatorAdapter extends AValidatorAdapter
{
    protected function buildValidator(): Validator
    {
        $validator = new Validator();

        $validator->addRule('code', function ($value) {
            if ($value !== null && (!is_string($value) || trim($value) === '')) {
                return 'Поле code должно быть непустой строкой';
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
