<?php

namespace crm\src\Investments\Source\_common\DTOs;

/**
 * DTO для источника в базе данных.
 */
class DbInvSourceDto
{
    public function __construct(
        public string $code,
        public string $label,
        public ?int $id = null,
    ) {
    }
}
