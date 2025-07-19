<?php

namespace crm\src\Investments\InvActivity;

use DateTimeImmutable;
use crm\src\Investments\InvActivity\_entities\DealType;
use crm\src\Investments\InvActivity\_entities\InvActivity;
use crm\src\Investments\InvActivity\CalculatePnlService;
use crm\src\Investments\InvActivity\_exceptions\InvActivityException;
use crm\src\Investments\InvActivity\_common\mappers\InvActivityMapper;
use crm\src\Investments\InvActivity\_common\adapters\InvActivityResult;
use crm\src\Investments\InvActivity\_common\interfaces\IInvActivityResult;
use crm\src\Investments\InvActivity\_common\interfaces\IInvActivityRepository;

final class CloseInvActivity
{
    public function __construct(
        private IInvActivityRepository $repository,
        private CalculatePnlService $pnlService
    ) {
    }

    /**
     * Закрывает сделку, фиксируя цену, время и результат.
     *
     * @param  InvActivity $invActivity
     * @param  DateTimeImmutable|null $closeTime
     * @param  float|null $closePrice
     * @return IInvActivityResult
     */
    public function handle(InvActivity $activity, ?DateTimeImmutable $closeTime = null, ?float $closePrice = null): IInvActivityResult
    {
        if (DealType::equals($activity->type, DealType::CLOSED)) {
            throw new InvActivityException("Сделка уже закрыта");
        }

        $activity->type = DealType::CLOSED;
        $activity->closeTime = $closeTime ?? new DateTimeImmutable();
        $activity->closePrice ??= $closePrice;

        if ($activity->closePrice === null) {
            return InvActivityResult::failure(
                new InvActivityException("Цена закрытия обязательна для закрытия сделки")
            );
        }

        $activity->result = $this->pnlService->calculateRawPnl($activity);

        $dbDto = InvActivityMapper::fromEntityToDb($activity);
        $result = $this->repository->update(InvActivityMapper::fromDbExtractFilledFields($dbDto));

        if (!$result->isSuccess()) {
            return InvActivityResult::failure(
                $result->getError() ?? new InvActivityException("Ошибка сохранения сделки")
            );
        }

        return InvActivityResult::success($activity);
    }
}
