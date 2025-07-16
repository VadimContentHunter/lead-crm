<?php

namespace crm\src\Investments\InvSource\_common\DTOs;

/**
 * DTO для входных данных источника.
 */
class InvSourceInputDto
{
    public function __construct(
        public ?string $code = null,
        public ?string $label = null,
        public ?int $id = null,
    ) {
    }
}
