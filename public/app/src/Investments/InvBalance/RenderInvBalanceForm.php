<?php

namespace crm\src\Investments\InvBalance;

use crm\src\Investments\InvBalance\_common\adapters\InvBalanceResult;
use crm\src\Investments\InvBalance\_common\interfaces\IInvBalanceResult;
use crm\src\Investments\InvBalance\_common\interfaces\IInvBalanceRepository;
use crm\src\Investments\InvBalance\_common\mappers\InvBalanceMapper;

final class RenderInvBalanceForm
{
    public function __construct(
        private IInvBalanceRepository $invBalanceRepo
    ) {
    }

    /**
     * @param array<string,mixed> $params
     */
    public function getBalanceFormData(array $params): IInvBalanceResult
    {
        $balanceRes = $this->invBalanceRepo->getByLeadUid(InvBalanceMapper::fromArrayToInput($params)->leadUid);
        if ($balanceRes->isEmpty()) {
            return InvBalanceResult::success([
                'lead_uid' => $balanceRes->getLeadUid(),
                'current' => 0.0,
                'deposit' => 0.0,
                'potential' => 0.0,
                'active' => 0.0,
            ]);
        }

        if (!$balanceRes->isSuccess()) {
            return InvBalanceResult::failure($balanceRes->getError() ?? new \RuntimeException("Ошибка при получении баланса"));
        }

        return InvBalanceResult::success([
            'lead_uid' => $balanceRes->getLeadUid(),
            'current' => $balanceRes->getCurrent(),
            'deposit' => $balanceRes->getDeposit(),
            'potential' => $balanceRes->getPotential(),
            'active' => $balanceRes->getActive(),
        ]);
    }
}
