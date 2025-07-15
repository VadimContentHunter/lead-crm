<?php

namespace crm\src\Investments\Deposit\_common\DTOs;

/**
 * DTO, представляющий депозит из БД.
 */
class DbInvDepositDto
{
    /**
     * @param int    $id      Идентификатор депозита
     * @param string $uid     UID лида
     * @param float  $sum     Сумма депозита
     * @param string $created Дата создания (в формате строки)
     */
    public function __construct(
        public int $id,
        public string $uid,
        public float $sum,
        public string $created,
    ) {
    }
}
