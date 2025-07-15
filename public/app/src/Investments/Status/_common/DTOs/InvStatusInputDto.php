<?php

namespace Domain\Investment\DTOs;

/**
 * DTO для входных данных при создании/редактировании статуса.
 */
class InvStatusInputDto
{
    public function __construct(
        public ?string $code = null,
        public ?string $label = null,
    ) {
    }
}
