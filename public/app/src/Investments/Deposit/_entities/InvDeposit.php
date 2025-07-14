<?php

namespace crm\src\Investments\Deposit\_entities;

use DateTimeImmutable;

/**
 * Депозит, связанный с инвестиционным лидом.
 */
class InvDeposit
{
    public DateTimeImmutable $createdAt;

    /**
     * @param string                 $id        Уникальный идентификатор депозита (например, UUID или числовой ID из БД)
     * @param string                 $leadId    UID лида, к которому относится депозит
     * @param float                  $sum       Сумма депозита
     * @param DateTimeImmutable|null $createdAt Дата и время создания депозита (по умолчанию — текущее)
     * @param string                 $comment   Комментарий к депозиту (опционально)
     */
    public function __construct(
        public string $id,
        public string $leadId,
        public float $sum,
        ?DateTimeImmutable $createdAt = null,
        public string $comment = '',
    ) {
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }
}
