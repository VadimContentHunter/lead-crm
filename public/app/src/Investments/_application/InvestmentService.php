<?php

namespace crm\src\Investments\_application;

use crm\src\Investments\InvLead\ManageInvLead;
use crm\src\Investments\InvSource\ManageInvSource;
use crm\src\Investments\InvStatus\ManageInvStatus;
use crm\src\Investments\InvLead\_common\mappers\InvLeadMapper;
use crm\src\Investments\InvLead\_common\adapters\InvLeadResult;
use crm\src\_common\adapters\Investments\SourceValidatorAdapter;
use crm\src\_common\adapters\Investments\StatusValidatorAdapter;
use crm\src\_common\adapters\Investments\InvLeadValidatorAdapter;
use crm\src\Investments\InvLead\_common\interfaces\IInvLeadResult;
use crm\src\Investments\InvSource\_common\mappers\InvSourceMapper;
use crm\src\Investments\InvStatus\_common\mappers\InvStatusMapper;
use crm\src\Investments\InvLead\_common\interfaces\IInvLeadRepository;
use crm\src\Investments\InvSource\_common\interfaces\IInvSourceResult;
use crm\src\Investments\InvStatus\_common\interfaces\IInvStatusResult;
use crm\src\Investments\InvSource\_common\interfaces\IInvSourceRepository;
use crm\src\Investments\InvStatus\_common\interfaces\IInvStatusRepository;
use crm\src\Investments\InvBalance\_common\interfaces\IInvBalanceRepository;
use crm\src\Investments\InvComment\_common\interfaces\IInvCommentRepository;
use crm\src\Investments\InvDeposit\_common\interfaces\IInvDepositRepository;
use crm\src\Investments\InvActivity\_common\interfaces\IInvActivityRepository;

final class InvestmentService
{
    private ManageInvLead $manageInvLead;

    private ManageInvSource $manageInvSource;

    private ManageInvStatus $manageInvStatus;

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
        $this->manageInvSource = new ManageInvSource($this->invSourceRepo, new SourceValidatorAdapter());
        $this->manageInvStatus = new ManageInvStatus($this->invStatusRepo, new StatusValidatorAdapter());
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createInvLead(array $data): IInvLeadResult
    {
        $resultUid = $this->manageInvLead->create(InvLeadMapper::fromArrayToInput($data));
        if ($resultUid->isSuccess()) {
            return $this->invLeadRepo->getByUid($resultUid->getString() ?? '');
        }

        return InvLeadResult::failure($resultUid->getError() ?? new \RuntimeException("Ошибка при создании лида"));
    }

    public function createInvSource(array $data): IInvSourceResult
    {
        return $this->manageInvSource->create(InvSourceMapper::fromArrayToInput($data));
    }

    public function createInvStatus(array $data): IInvStatusResult
    {
        return $this->manageInvStatus->create(InvStatusMapper::fromArrayToInput($data));
    }
}
