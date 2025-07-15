<?php

namespace crm\src\Investments\InvActivity\_common\DTOs;

/**
 * Входной DTO для создания или обновления инвестиционной сделки.
 * Все поля опциональны и допускают null.
 */
class InvActivityInputDto
{
    /**
     * @param int|null    $id              Идентификатор сделки (если обновляется)
     * @param string|null $InvActivityHash Уникальный хеш сделки
     * @param string|null $leadUid         UID лида
     * @param string|null $type            Тип сделки: 'active' или 'closed'
     * @param string|null $openTime        Время открытия (строка формата 'Y-m-d H:i:s')
     * @param string|null $closeTime       Время закрытия (строка формата 'Y-m-d H:i:s')
     * @param string|null $pair            Торговая пара
     * @param float|null  $openPrice       Цена открытия
     * @param float|null  $closePrice      Цена закрытия
     * @param float|null  $amount          Объём сделки
     * @param string|null $direction       Направление сделки: 'long' или 'short'
     * @param float|null  $result          Прибыль или убыток
     */
    public function __construct(
        public ?int $id = null,
        public ?string $InvActivityHash = null,
        public ?string $leadUid = null,
        public ?string $type = null,
        public ?string $openTime = null,
        public ?string $closeTime = null,
        public ?string $pair = null,
        public ?float $openPrice = null,
        public ?float $closePrice = null,
        public ?float $amount = null,
        public ?string $direction = null,
        public ?float $result = null,
    ) {
    }
}
