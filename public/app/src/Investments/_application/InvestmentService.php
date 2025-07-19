<?php

namespace crm\src\Investments\_application;

use crm\src\Investments\InvLead\ManageInvLead;
use crm\src\Investments\InvSource\DeleteSource;
use crm\src\Investments\InvStatus\DeleteStatus;
use crm\src\Investments\InvLead\RenderInvLeadForm;
use crm\src\Investments\InvSource\ManageInvSource;
use crm\src\Investments\InvStatus\ManageInvStatus;
use crm\src\Investments\InvBalance\ManageInvBalance;
use crm\src\Investments\InvActivity\CloseInvActivity;
use crm\src\Investments\InvLead\InvLeadTableRenderer;
use crm\src\Investments\InvActivity\CreateInvActivity;
use crm\src\Investments\InvActivity\DeleteInvActivity;
use crm\src\Investments\InvActivity\ManageInvActivity;
use crm\src\Investments\InvActivity\UpdateInvActivity;
use crm\src\Investments\InvActivity\CalculatePnlService;
use crm\src\Investments\InvBalance\RenderInvBalanceForm;
use crm\src\Investments\InvSource\InvSourceTableRenderer;
use crm\src\Investments\InvStatus\InvStatusTableRenderer;
use crm\src\Investments\InvActivity\RenderInvActivityForm;
use crm\src\Investments\InvActivity\InvActivityTableRenderer;
use crm\src\Investments\InvLead\_common\mappers\InvLeadMapper;
use crm\src\_common\adapters\Investments\SourceValidatorAdapter;
use crm\src\_common\adapters\Investments\StatusValidatorAdapter;
use crm\src\_common\adapters\Investments\InvLeadValidatorAdapter;
use crm\src\Investments\InvLead\_common\interfaces\IInvLeadResult;
use crm\src\Investments\InvSource\_common\mappers\InvSourceMapper;
use crm\src\Investments\InvStatus\_common\mappers\InvStatusMapper;
use crm\src\_common\adapters\Investments\InvBalanceValidatorAdapter;
use crm\src\_common\adapters\Investments\InvActivityValidatorAdapter;
use crm\src\Investments\InvActivity\_common\mappers\InvActivityMapper;
use crm\src\Investments\InvLead\_common\interfaces\IInvLeadRepository;
use crm\src\Investments\InvSource\_common\interfaces\IInvSourceResult;
use crm\src\Investments\InvStatus\_common\interfaces\IInvStatusResult;
use crm\src\Investments\InvBalance\_common\interfaces\IInvBalanceResult;
use crm\src\Investments\InvActivity\_common\interfaces\IInvActivityResult;
use crm\src\Investments\InvSource\_common\interfaces\IInvSourceRepository;
use crm\src\Investments\InvStatus\_common\interfaces\IInvStatusRepository;
use crm\src\Investments\InvBalance\_common\interfaces\IInvBalanceRepository;
use crm\src\Investments\InvComment\_common\interfaces\IInvCommentRepository;
use crm\src\Investments\InvDeposit\_common\interfaces\IInvDepositRepository;
use crm\src\Investments\InvActivity\_common\interfaces\IInvActivityRepository;
use crm\src\Investments\InvLead\_common\interfaces\IInvAccountManagerRepository;

final class InvestmentService
{
    private ManageInvLead $manageInvLead;
    private ManageInvSource $manageInvSource;
    private ManageInvStatus $manageInvStatus;
    private ManageInvBalance $manageInvBalance;
    private ManageInvActivity $manageInvActivity;

    public function __construct(
        private IInvActivityRepository $invActivityRepo,
        private IInvBalanceRepository $invBalanceRepo,
        private IInvCommentRepository $invCommentRepo,
        private IInvDepositRepository $invDepositRepo,
        private IInvLeadRepository $invLeadRepo,
        private IInvSourceRepository $invSourceRepo,
        private IInvStatusRepository $invStatusRepo,
        private IInvAccountManagerRepository $invAccountManagerRepo
    ) {
        $this->manageInvLead = new ManageInvLead(
            repository: $this->invLeadRepo,
            invSourceRepo: $this->invSourceRepo,
            invStatusRepo: $this->invStatusRepo,
            accountManagerRepo: $this->invAccountManagerRepo,
            validator: new InvLeadValidatorAdapter()
        );
        $this->manageInvSource = new ManageInvSource($this->invSourceRepo, new SourceValidatorAdapter());
        $this->manageInvStatus = new ManageInvStatus($this->invStatusRepo, new StatusValidatorAdapter());
        $this->manageInvBalance = new ManageInvBalance($this->invBalanceRepo, new InvBalanceValidatorAdapter());
        $this->manageInvActivity = new ManageInvActivity(
            create: new CreateInvActivity($this->invActivityRepo, new InvActivityValidatorAdapter(), new CalculatePnlService()),
            update: new UpdateInvActivity($this->invActivityRepo, new InvActivityValidatorAdapter()),
            delete: new DeleteInvActivity($this->invActivityRepo),
            close: new CloseInvActivity($this->invActivityRepo, new CalculatePnlService()),
            repository: $this->invActivityRepo,
            invLeadRepo: $this->invLeadRepo
        );
    }

    // === CRUD: Лиды ===

    /**
     * @param array<string, mixed> $data
     */
    public function createInvLead(array $data): IInvLeadResult
    {
        return $this->manageInvLead->createLeadWithReturn(InvLeadMapper::fromArrayToInput($data));
    }

    /**
     * @param array<string,mixed> $data
     */
    public function updateInvLead(array $data): IInvLeadResult
    {
        return $this->manageInvLead->updateByUid(InvLeadMapper::fromArrayToInput($data));
    }

    public function getAllLead(): IInvLeadResult
    {
        return $this->invLeadRepo->getAll()->mapEach([InvLeadMapper::class, 'fromDbToEntity']);
    }

    public function getInvLeadTable(): IInvLeadResult
    {
        $invLeadTableRenderer = new InvLeadTableRenderer(
            $this->invLeadRepo,
            $this->invSourceRepo,
            $this->invStatusRepo,
            $this->invAccountManagerRepo
        );
        return $invLeadTableRenderer->getBaseTable();
    }

    /**
     * Возвращает данные для формы создания лида.
     *
     * @param array<string,mixed> $params
     * @param array<string,mixed> $extraData Данные, которые нужно добавить/переопределить в итоговом массиве
     */
    public function getFormCreateData(
        array $params,
        array $extraData = [],
    ): IInvLeadResult {
        $renderInvLeadForm = new RenderInvLeadForm(
            invLeadRepo: $this->invLeadRepo,
            invStatusRepo: $this->invStatusRepo,
            invSourceRepo: $this->invSourceRepo,
            accountManagerRepo: $this->invAccountManagerRepo
        );

        return $renderInvLeadForm->getFormCreateData($params, $extraData);
    }


    // === CRUD: Источники ===

    /**
     * @param array<string,mixed> $data
     */
    public function createInvSource(array $data): IInvSourceResult
    {
        return $this->manageInvSource->create(InvSourceMapper::fromArrayToInput($data));
    }

    public function getSourceTable(): IInvSourceResult
    {
        return (new InvSourceTableRenderer($this->invSourceRepo))->getBaseTable();
    }

    /**
     * @param array<string,mixed> $data
     */
    public function updateSource(array $data): IInvSourceResult
    {
        return $this->manageInvSource->updateById(InvSourceMapper::fromArrayToInput($data));
    }

    /**
     * @param array<string,mixed> $data
     */
    public function deleteSource(array $data): IInvSourceResult
    {
        return (new DeleteSource($this->invSourceRepo))->deleteSource($data);
    }

    // === CRUD: Статусы ===

    /**
     * @param array<string,mixed> $data
     */
    public function createInvStatus(array $data): IInvStatusResult
    {
        return $this->manageInvStatus->create(InvStatusMapper::fromArrayToInput($data));
    }

    /**
     * @param array<string,mixed> $data
     */
    public function updateStatus(array $data): IInvStatusResult
    {
        return $this->manageInvStatus->updateById(InvStatusMapper::fromArrayToInput($data));
    }

    /**
     * @param array<string,mixed> $data
     */
    public function deleteStatus(array $data): IInvStatusResult
    {
        return (new DeleteStatus($this->invStatusRepo))->deleteStatus($data);
    }

    public function getStatusTable(): IInvStatusResult
    {
        return (new InvStatusTableRenderer($this->invStatusRepo))->getBaseTable();
    }

    // === CRUD: Балансы ===

    /**
     * @param array<string,mixed> $data
     */
    public function createOrUpdateInvBalance(array $data): IInvBalanceResult
    {
        return $this->manageInvBalance->createOrUpdateInvBalance($data);
    }

     /**
      * @param array<string,mixed> $params
      */
    public function getBalanceData(array $params): IInvBalanceResult
    {
        return (new RenderInvBalanceForm($this->invBalanceRepo))->getBalanceFormData($params);
    }

    // === CRUD: Активности ===

    /**
     * @return IInvActivityResult
     */
    public function getActivityTable(): IInvActivityResult
    {
        return $this->manageInvActivity->getActivityTable();
    }

    /**
     * @param array<string,mixed> $data
     */
    public function createActivity(array $data): IInvActivityResult
    {
        return $this->manageInvActivity->create($data);
    }

    /**
     * @param array<string,mixed> $params
     */
    public function getActivityData(array $params): IInvActivityResult
    {
        return  $this->manageInvActivity->getActivityData($params);
    }
}
