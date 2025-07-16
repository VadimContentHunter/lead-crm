<?php

namespace crm\src\Investments\Lead\_entities;

use DateTimeImmutable;
use crm\src\Investments\Lead\_entities\SimpleInvLead;
use crm\src\Investments\InvSource\_entities\InvSource;
use crm\src\Investments\Status\_entities\InvStatus;
use crm\src\Investments\InvBalance\_entities\InvBalance;
use crm\src\Investments\InvComment\_common\InvCommentCollection;
use crm\src\Investments\InvDeposit\_common\InvDepositCollection;
use crm\src\Investments\InvActivity\_common\InvActivityCollection;

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
     * @param InvBalance|null       $InvBalance     Объект баланса клиента
     * @param InvDepositCollection  $deposits       Коллекция депозитов клиента
     * @param InvActivityCollection $activities     Коллекция инвестиционных сделок
     * @param InvCommentCollection  $InvComments    Коллекция комментариев по лиду
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
        public ?InvBalance $InvBalance = null,
        public InvDepositCollection $deposits = new InvDepositCollection(),
        public InvActivityCollection $activities = new InvActivityCollection(),
        public InvCommentCollection $InvComments = new InvCommentCollection(),
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

        $this->InvBalance = $this->InvBalance ?? new InvBalance(leadUid: $uid);
    }
}
