<?php

namespace crm\src\Investments\InvStatus\_common\DTOs;

/**
 * DTO для хранения статуса в БД.
 */
class DbInvStatusDto
{
    public function __construct(
        public string $code,
        public string $label,
        public ?int $id = null,
    ) {
    }
}
