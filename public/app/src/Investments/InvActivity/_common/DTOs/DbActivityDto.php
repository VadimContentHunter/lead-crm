<?php

namespace crm\src\Investments\InvActivity\_common\DTOs;

/**
 * DTO для хранения сделки в базе данных.
 * Используется для передачи данных между слоем хранения и доменной сущностью.
 */
class DbInvActivityDto
{
    /**
     * @param string      $InvActivity_hash Уникальный хеш сделки
     * @param string      $lead_uid      UID лида, к которому привязана сделка
     * @param string      $type          Тип сделки: 'active' или 'closed'
     * @param string      $open_time     Время открытия сделки (формат: Y-m-d H:i:s)
     * @param string|null $close_time    Время закрытия сделки (если есть)
     * @param string      $pair          Торговая пара, например: 'BTC/USDT'
     * @param float       $open_price    Цена актива при открытии
     * @param float|null  $close_price   Цена актива при закрытии
     * @param float       $amount        Объём сделки
     * @param string      $direction     Направление сделки: 'long' или 'short'
     * @param float|null  $result        Прибыль или убыток (если сделка закрыта)
     * @param int|null    $id            Уникальный числовой идентификатор сделки в базе (если есть)
     */
    public function __construct(
        public string $InvActivity_hash,
        public string $lead_uid,
        public string $type,
        public string $open_time,
        public ?string $close_time,
        public string $pair,
        public float $open_price,
        public ?float $close_price,
        public float $amount,
        public string $direction,
        public ?float $result,
        public ?int $id = null,
    ) {
    }
}
