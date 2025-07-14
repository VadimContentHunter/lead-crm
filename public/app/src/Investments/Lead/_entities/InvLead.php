<?php

namespace crm\src\Investments\Lead\_entities;

use crm\src\Investments\Balance\_entities\InvBalance;
use crm\src\Investments\Comment\_common\InvCommentCollection;
use crm\src\Investments\Deposit\_common\InvDepositCollection;
use crm\src\Investments\Activity\_common\InvActivityCollection;
use crm\src\Investments\Source\_entities\InvSource;
use Domain\Investment\InvStatus;

/**
 * Инвестиционный лид — основная сущность CRM-модуля инвестиций.
 */
class InvLead
{
    /**
     * @param string                $uid            Уникальный 9-значный идентификатор, начинается с "928"
     * @param string                $contact        Имя или контактное лицо
     * @param string                $phone          Телефон клиента
     * @param string                $email          Email клиента
     * @param string                $fullName       Полное имя клиента
     * @param int                   $createdAt      Время создания (UNIX timestamp)
     * @param string                $accountManager Имя закреплённого менеджера (внешний справочник)
     * @param bool                  $visible        Флаг видимости лида (по умолчанию true)
     * @param InvSource|null        $source         Источник лида (например, Bybit, Binance) — внешний справочник
     * @param InvStatus|null        $status         Текущий статус лида (например: "work", "lost") — внешний справочник
     * @param InvBalance|null       $balance        Объект баланса клиента (текущий, депозит, актив, потенциал)
     * @param InvDepositCollection  $deposits       Коллекция депозитов клиента
     * @param InvActivityCollection $activities     Коллекция сделок (активных/закрытых)
     * @param InvCommentCollection  $comments       Комментарии по лиду (связанные с менеджерами/системой)
     */
    public function __construct(
        public string $uid,
        public string $contact = '',
        public string $phone = '',
        public string $email = '',
        public string $fullName = '',
        public int $createdAt = 0,
        public string $accountManager = '',
        public bool $visible = true,
        public ?InvSource $source = null,
        public ?InvStatus $status = null,
        public ?InvBalance $balance = null,
        public InvDepositCollection $deposits = new InvDepositCollection(),
        public InvActivityCollection $activities = new InvActivityCollection(),
        public InvCommentCollection $comments = new InvCommentCollection(),
    ) {
        // Если баланс явно не передан — создаём с привязкой к UID
        $this->balance = $balance ?? new InvBalance(leadUid: $uid);
    }
}
