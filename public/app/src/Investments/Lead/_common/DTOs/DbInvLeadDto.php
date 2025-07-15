<?php

namespace crm\src\Investments\Lead\_dto;

/**
 * DTO, отражающий структуру хранения инвестиционного лида в базе данных.
 */
class DbInvLeadDto
{
    /**
     * @param string      $uid
     * @param string      $createdAt      Время создания (формат: Y-m-d H:i:s)
     * @param string      $contact
     * @param string      $phone
     * @param string      $email
     * @param string      $fullName
     * @param string|null $accountManager
     * @param bool        $visible
     * @param int|null    $sourceId
     * @param int|null    $statusId
     * @param int|null    $InvBalanceId
     */
    public function __construct(
        public string $uid,
        public string $createdAt,
        public string $contact = '',
        public string $phone = '',
        public string $email = '',
        public string $fullName = '',
        public ?string $accountManager = null,
        public bool $visible = true,
        public ?int $sourceId = null,
        public ?int $statusId = null,
        public ?int $InvBalanceId = null,
    ) {
    }
}
