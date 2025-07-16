<?php

namespace crm\src\Investments\InvLead\_common\DTOs;

/**
 * DTO, отражающий структуру хранения инвестиционного лида в базе данных.
 * Используется в слое хранения (например, в репозиториях).
 */
class DbInvLeadDto
{
    /**
     * @param string      $uid                 Уникальный идентификатор (например, "928000001")
     * @param string      $createdAt           Время создания (формат: Y-m-d H:i:s)
     * @param string      $contact             Контактное лицо
     * @param string      $phone               Телефон клиента
     * @param string      $email               Email клиента
     * @param string      $fullName            Полное имя
     * @param int|null    $accountManagerId    ID аккаунт-менеджера
     * @param string|null $accountManagerLogin Логин аккаунт-менеджера
     * @param bool        $visible             Видимость лида
     * @param int|null    $sourceId            ID источника
     * @param int|null    $statusId            ID статуса
     * @param int|null    $InvBalanceId        ID баланса
     */
    public function __construct(
        public string $uid,
        public string $createdAt,
        public string $contact = '',
        public string $phone = '',
        public string $email = '',
        public string $fullName = '',
        public ?int $accountManagerId = null,
        public ?string $accountManagerLogin = null,
        public bool $visible = true,
        public ?int $sourceId = null,
        public ?int $statusId = null,
        public ?int $InvBalanceId = null,
    ) {
    }
}
