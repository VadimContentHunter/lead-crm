<?php

namespace crm\src\Investments\_application;

use crm\src\Investments\InvLead\ManageInvLead;
use crm\src\Investments\InvLead\_common\mappers\InvLeadMapper;
use crm\src\Investments\InvLead\_common\adapters\InvLeadResult;
use crm\src\_common\adapters\Investments\InvLeadValidatorAdapter;
use crm\src\Investments\InvLead\_common\interfaces\IInvLeadResult;
use crm\src\Investments\InvLead\_common\interfaces\IInvLeadRepository;
use crm\src\Investments\InvSource\_common\interfaces\IInvSourceRepository;
use crm\src\Investments\InvStatus\_common\interfaces\IInvStatusRepository;
use crm\src\Investments\InvBalance\_common\interfaces\IInvBalanceRepository;
use crm\src\Investments\InvComment\_common\interfaces\IInvCommentRepository;
use crm\src\Investments\InvDeposit\_common\interfaces\IInvDepositRepository;
use crm\src\Investments\InvActivity\_common\interfaces\IInvActivityRepository;

final class InvestmentService
{
    private ManageInvLead $manageInvLead;

    public function __construct(
        private IInvActivityRepository $invActivityRepo,
        private IInvBalanceRepository $invBalanceRepo,
        private IInvCommentRepository $invCommentRepo,
        private IInvDepositRepository $invDepositRepo,
        private IInvLeadRepository $invLeadRepo,
        private IInvSourceRepository $invSourceRepo,
        private IInvStatusRepository $invStatusRepo
    ) {
        $this->manageInvLead = new ManageInvLead($this->invLeadRepo, new InvLeadValidatorAdapter());
    }

    public function createInvLead(array $data): IInvLeadResult
    {
        $createAction = $this->manageInvLead->create(InvLeadMapper::fromArrayToInput($data));
        if ($createAction->isSuccess()) {
            return InvLeadResult::success($this->invLeadRepo->getByUid($createAction->getUid() ?? ''));
        }

        return InvLeadResult::failure($createAction->getError());
    }
}
