<?php

namespace crm\src\Investments\InvActivity\_entities;

use DateTimeImmutable;

/**
 * Класс-сущность инвестиционной сделки.
 */
class InvInvActivity
{
    /**
     * @param string                 $InvActivityHash Уникальный хеш-идентификатор сделки
     * @param string                 $leadUid         UID лида, к которому относится сделка
     * @param DealType               $type            Тип сделки (active или closed)
     * @param string                 $pair            Торговая пара (например, "BTC/USD")
     * @param float                  $openPrice       Цена при открытии
     * @param float                  $amount          Объём сделки
     * @param DealDirection          $direction       Направление сделки (long/short)
     * @param DateTimeImmutable|null $openTime        Время открытия сделки (по умолчанию — текущее)
     * @param DateTimeImmutable|null $closeTime       Время закрытия сделки (если есть)
     * @param float|null             $closePrice      Цена при закрытии (если есть)
     * @param float|null             $result          Прибыль или убыток (если закрыта)
     * @param int|null               $id              Уникальный ID сделки (если есть)
     */
    public function __construct(
        public string $InvActivityHash,
        public string $leadUid,
        public DealType $type,
        public string $pair,
        public float $openPrice,
        public float $amount,
        public DealDirection $direction,
        public ?DateTimeImmutable $openTime = null,
        public ?DateTimeImmutable $closeTime = null,
        public ?float $closePrice = null,
        public ?float $result = null,
        public ?int $id = null,
    ) {
        $this->openTime = $openTime ?? new DateTimeImmutable();
    }

    public function isClosed(): bool
    {
        return $this->type === DealType::CLOSED;
    }

    public function isActive(): bool
    {
        return $this->type === DealType::ACTIVE;
    }

    /**
     * Закрывает сделку, фиксируя цену, время и результат.
     *
     * @param float                  $closePrice Цена при закрытии
     * @param DateTimeImmutable|null $closeTime  Время закрытия сделки (по умолчанию — текущее)
     *
     * @return void
     */
    public function close(float $closePrice, ?DateTimeImmutable $closeTime = null): void
    {
        if ($this->isClosed()) {
            throw new \LogicException("Сделка уже закрыта");
        }

        $this->type = DealType::CLOSED;
        $this->closeTime = $closeTime ?? new DateTimeImmutable();
        $this->closePrice = $closePrice;
        $this->result = $this->calculateResult();
    }

    /**
     * Вычисляет результат сделки (прибыль или убыток).
     *
     * @return float
     */
    private function calculateResult(): float
    {
        if ($this->closePrice === null) {
            throw new \LogicException("Цена закрытия не задана");
        }

        $delta = $this->closePrice - $this->openPrice;
        $multiplier = $this->direction === DealDirection::SHORT ? -1 : 1;

        return $delta * $this->amount * $multiplier;
    }
}
