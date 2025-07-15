<?php

namespace crm\src\Investments\Deposit\_common\DTOs;

/**
 * DTO для входящих данных по депозиту.
 */
class InvDepositInputDto
{
    /**
     * @param int|null    $id      ID депозита (опционально)
     * @param string|null $uid     UID лида
     * @param float|null  $sum     Сумма депозита
     * @param string|null $created Дата создания (опционально)
     */
    public function __construct(
        public ?int $id = null,
        public ?string $uid = null,
        public ?float $sum = null,
        public ?string $created = null
    ) {
    }
}
