<?php

namespace crm\src\components\LeadManagement\_common\DTOs;

use DateTimeImmutable;

/**
 * DTO для работы с таблицей leads.
 * Используется при загрузке из БД и сохранении в БД.
 */
class LeadDbDto
{
    public function __construct(
        public ?int $id = null,
        public string $fullName = '',
        public string $address = '',
        public string $contact = '',
        public ?int $sourceId = null,
        public ?int $statusId = null,
        public ?int $accountManagerId = null,
        public ?DateTimeImmutable $createdAt = null,
    ) {
    }
}
