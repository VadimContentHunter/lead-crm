<?php

namespace crm\src\Investments\InvLead\_common\DTOs;

/**
 * DTO для создания или редактирования инвестиционного лида.
 * Все поля являются необязательными и могут быть null.
 */
class InvLeadInputDto
{
    /**
     * @param string|null $uid                 Уникальный идентификатор (928...)
     * @param string|null $contact             Контактное лицо
     * @param string|null $phone               Телефон клиента
     * @param string|null $email               Email клиента
     * @param string|null $fullName            Полное имя
     * @param int|null    $accountManagerId    ID аккаунт-менеджера
     * @param string|null $accountManagerLogin Логин менеджера
     * @param bool|null   $visible             Видимость лида
     * @param int|null    $sourceId            ID источника
     * @param int|null    $statusId            ID статуса
     */
    public function __construct(
        public ?string $uid = null,
        public ?string $contact = null,
        public ?string $phone = null,
        public ?string $email = null,
        public ?string $fullName = null,
        public ?int $accountManagerId = null,
        public ?string $accountManagerLogin = null,
        public ?bool $visible = null,
        public ?int $sourceId = null,
        public ?int $statusId = null,
    ) {
    }
}
