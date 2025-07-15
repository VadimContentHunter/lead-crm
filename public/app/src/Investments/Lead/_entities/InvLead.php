<?php

namespace crm\src\Investments\Lead\_entities;

use DateTimeImmutable;
use crm\src\Investments\Lead\_entities\SimpleInvLead;
use crm\src\Investments\Source\_entities\InvSource;
use crm\src\Investments\Status\_entities\InvStatus;
use crm\src\Investments\Balance\_entities\InvBalance;
use crm\src\Investments\Comment\_common\InvCommentCollection;
use crm\src\Investments\Deposit\_common\InvDepositCollection;
use crm\src\Investments\InvActivity\_common\InvInvActivityCollection;

/**
 * Полная инвестиционная модель лида, включающая связанные данные: баланс, депозиты, сделки, комментарии.
 */
class InvLead extends SimpleInvLead
{
    /**
     * @param string                $uid            Уникальный 9-значный идентификатор, начинается с "928"
     * @param string                $contact        Контактное лицо или имя
     * @param string                $phone          Телефон клиента
     * @param string                $email          Email клиента
     * @param string                $fullName       Полное имя клиента
     * @param DateTimeImmutable|null $createdAt      Время создания (если null — установится текущее)
     * @param string                $accountManager Имя закреплённого менеджера
     * @param bool                  $visible        Видимость лида (по умолчанию true)
     * @param InvSource|null        $source         Источник лида (например, Binance, Bybit)
     * @param InvStatus|null        $status         Текущий статус лида (например, "work", "lost")
     * @param InvBalance|null       $balance        Объект баланса клиента
     * @param InvDepositCollection  $deposits       Коллекция депозитов клиента
     * @param InvInvActivityCollection $activities     Коллекция инвестиционных сделок
     * @param InvCommentCollection  $comments       Коллекция комментариев по лиду
     */
    public function __construct(
        string $uid,
        string $contact = '',
        string $phone = '',
        string $email = '',
        string $fullName = '',
        ?DateTimeImmutable $createdAt = null,
        string $accountManager = '',
        bool $visible = true,
        ?InvSource $source = null,
        ?InvStatus $status = null,
        public ?InvBalance $balance = null,
        public InvDepositCollection $deposits = new InvDepositCollection(),
        public InvInvActivityCollection $activities = new InvInvActivityCollection(),
        public InvCommentCollection $comments = new InvCommentCollection(),
    ) {
        parent::__construct(
            $uid,
            $contact,
            $phone,
            $email,
            $fullName,
            $createdAt,
            $accountManager,
            $visible,
            $source,
            $status
        );

        $this->balance = $this->balance ?? new InvBalance(leadUid: $uid);
    }
}
