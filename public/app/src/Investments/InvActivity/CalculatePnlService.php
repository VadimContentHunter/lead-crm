<?php

namespace crm\src\Investments\InvActivity;

use crm\src\Investments\InvActivity\_entities\InvActivity;
use crm\src\Investments\InvActivity\_entities\DealDirection;
use crm\src\Investments\InvActivity\_common\DTOs\InvActivityInputDto;
use crm\src\Investments\InvActivity\_exceptions\InvActivityException;
use crm\src\Investments\InvActivity\_common\mappers\InvActivityMapper;

final class CalculatePnlService
{
    /**
     * Вычисляет результат сделки (прибыль или убыток).
     *
     * @param  InvActivity $activity
     * @return float
     */
    public function calculateRawPnl(InvActivity|InvActivityInputDto $activity): float
    {
        $entity = $this->ensureEntity($activity);

        if ($entity->closePrice === null) {
            throw new InvActivityException("Цена закрытия не задана");
        }

        $delta = $entity->closePrice - $entity->openPrice;
        $multiplier = $entity->direction === DealDirection::SHORT ? -1 : 1;

        return $delta * $entity->amount * $multiplier;
    }

    public function calculateWithLeverage(InvActivity|InvActivityInputDto $activity, ?float $leverage = null): float
    {
        $raw = $this->calculateRawPnl($activity);
        return $leverage !== null ? $raw * $leverage : $raw;
    }

    private function ensureEntity(InvActivity|InvActivityInputDto $activity): InvActivity
    {
        return $activity instanceof InvActivity
        ? $activity
        : InvActivityMapper::fromInputToEntity($activity);
    }
}
