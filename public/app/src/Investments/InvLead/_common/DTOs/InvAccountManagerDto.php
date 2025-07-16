<?php

namespace crm\src\Investments\InvLead\_common\DTOs;

/**
 * DTO для хранения инвестиционного аккаунт-менеджера.
 * Используется при работе с хранилищем или маппингом.
 */
class InvAccountManagerDto
{
    /**
     * @param int    $id    Уникальный идентификатор менеджера
     * @param string $login Логин/имя пользователя менеджера
     */
    public function __construct(
        public int $id,
        public string $login,
    ) {
    }
}
