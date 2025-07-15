<?php

namespace crm\src\Investments\Lead\_entities;

use DateTimeImmutable;
use crm\src\Investments\Source\_entities\InvSource;
use crm\src\Investments\Status\_entities\InvStatus;

/**
 * Упрощённая версия инвестиционного лида для использования в CRUD, таблицах и фильтрации.
 */
class SimpleInvLead
{
    /**
     * @param string                $uid            Уникальный идентификатор (например, "928000001")
     * @param string                $contact        Контактное лицо или имя
     * @param string                $phone          Телефон клиента
     * @param string                $email          Email клиента
     * @param string                $fullName       Полное имя клиента
     * @param DateTimeImmutable|null $createdAt      Время создания
     * @param string                $accountManager Имя закреплённого менеджера
     * @param bool                  $visible        Видимость лида (по умолчанию true)
     * @param InvSource|null        $source         Источник лида (например, Binance, Bybit)
     * @param InvStatus|null        $status         Текущий статус лида (например, "work", "lost")
     */
    public function __construct(
        public string $uid,
        public string $contact = '',
        public string $phone = '',
        public string $email = '',
        public string $fullName = '',
        public ?DateTimeImmutable $createdAt = null,
        public string $accountManager = '',
        public bool $visible = true,
        public ?InvSource $source = null,
        public ?InvStatus $status = null,
    ) {
        $this->createdAt ??= new DateTimeImmutable();
    }
}
