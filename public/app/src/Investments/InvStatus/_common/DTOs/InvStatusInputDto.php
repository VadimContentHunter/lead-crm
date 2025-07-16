<?php

namespace crm\src\Investments\InvStatus\_common\DTOs;

/**
 * DTO для входных данных при создании/редактировании статуса.
 */
class InvStatusInputDto
{
    public function __construct(
        public ?string $code = null,
        public ?string $label = null,
        public ?int $id = null,
    ) {
    }
}
