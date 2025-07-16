<?php

namespace crm\src\Investments\InvDeposit\_entities;

use DateTimeImmutable;

/**
 * Депозит, связанный с инвестиционным лидом.
 */
class InvDeposit
{
    public DateTimeImmutable $createdAt;

    /**
     * @param int                    $id        Числовой ID депозита из БД
     * @param string                 $uid       UID лида, к которому относится депозит
     * @param float                  $sum       Сумма депозита
     * @param DateTimeImmutable|null $createdAt Дата и время создания депозита (по умолчанию — текущее)
     */
    public function __construct(
        public int $id,
        public string $uid,
        public float $sum,
        ?DateTimeImmutable $createdAt = null,
    ) {
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }
}
