<?php

namespace Domain\Investment\DTOs;

/**
 * DTO для хранения статуса в БД.
 */
class DbInvStatusDto
{
    public function __construct(
        public ?int $id = null,
        public string $code,
        public string $label,
    ) {
    }
}
