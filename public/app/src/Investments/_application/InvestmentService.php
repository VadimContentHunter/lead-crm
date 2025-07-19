<?php

namespace crm\src\Investments\_application;

use crm\src\Investments\InvLead\ManageInvLead;
use crm\src\Investments\InvSource\DeleteSource;
use crm\src\Investments\InvStatus\DeleteStatus;
use crm\src\services\TableRenderer\TableFacade;
use crm\src\Investments\InvLead\_entities\InvLead;
use crm\src\Investments\InvLead\RenderInvLeadForm;
use crm\src\Investments\InvSource\ManageInvSource;
use crm\src\Investments\InvStatus\ManageInvStatus;
use crm\src\services\TableRenderer\TableDecorator;
use crm\src\Investments\InvBalance\ManageInvBalance;
use crm\src\services\TableRenderer\TableRenderInput;
use crm\src\Investments\InvLead\InvLeadTableRenderer;
use crm\src\Investments\InvActivity\ManageInvActivity;
use crm\src\Investments\InvActivity\_entities\DealType;
use crm\src\Investments\InvLead\_entities\SimpleInvLead;
use crm\src\Investments\InvSource\InvSourceTableRenderer;
use crm\src\Investments\InvStatus\InvStatusTableRenderer;
use crm\src\services\TableRenderer\TypedTableTransformer;
use crm\src\Investments\InvActivity\_entities\InvActivity;
use crm\src\Investments\InvLead\_common\DTOs\DbInvLeadDto;
use crm\src\Investments\_application\adapters\InvestResult;
use crm\src\Investments\InvActivity\_entities\DealDirection;
use crm\src\Investments\_application\interfaces\IInvestResult;
use crm\src\Investments\InvLead\_common\mappers\InvLeadMapper;
use crm\src\Investments\InvSource\_common\DTOs\DbInvSourceDto;
use crm\src\Investments\InvStatus\_common\DTOs\DbInvStatusDto;
use crm\src\Investments\InvLead\_common\adapters\InvLeadResult;
use crm\src\_common\adapters\Investments\SourceValidatorAdapter;
use crm\src\_common\adapters\Investments\StatusValidatorAdapter;
use crm\src\_common\adapters\Investments\InvLeadValidatorAdapter;
use crm\src\Investments\InvActivity\_common\DTOs\DbInvActivityDto;
use crm\src\Investments\InvLead\_common\DTOs\InvAccountManagerDto;
use crm\src\Investments\InvLead\_common\interfaces\IInvLeadResult;
use crm\src\Investments\InvSource\_common\mappers\InvSourceMapper;
use crm\src\Investments\InvStatus\_common\mappers\InvStatusMapper;
use crm\src\Investments\InvSource\_common\adapters\InvSourceResult;
use crm\src\Investments\InvStatus\_common\adapters\InvStatusResult;
use crm\src\_common\adapters\Investments\InvBalanceValidatorAdapter;
use crm\src\Investments\InvBalance\_common\mappers\InvBalanceMapper;
use crm\src\_common\adapters\Investments\InvActivityValidatorAdapter;
use crm\src\Investments\InvBalance\_common\adapters\InvBalanceResult;
use crm\src\services\TableRenderer\typesTransform\TextInputTransform;
use crm\src\Investments\InvActivity\_common\mappers\InvActivityMapper;
use crm\src\Investments\InvLead\_common\interfaces\IInvLeadRepository;
use crm\src\Investments\InvSource\_common\interfaces\IInvSourceResult;
use crm\src\Investments\InvStatus\_common\interfaces\IInvStatusResult;
use crm\src\Investments\InvActivity\_common\adapters\InvActivityResult;
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
        $this->manageInvActivity = new ManageInvActivity($this->invActivityRepo, new InvActivityValidatorAdapter());
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
        $data['uid'] = isset($data['uid'])  ? (string) $data['uid']
                                            : (isset($data['lead_uid']) ? (string) $data['lead_uid'] : 0);

        $balanceRes = $this->invBalanceRepo->getByLeadUid((string)$data['uid'])->first();
        if ($balanceRes->isSuccess()) {
            return $this->manageInvBalance->updateByLeadUid(InvBalanceMapper::fromArrayToInput($data));
        }
        return $this->manageInvBalance->create(InvBalanceMapper::fromArrayToInput($data));
    }

    // === CRUD: Активности ===

    /**
     * @return IInvActivityResult
     */
    public function getActivityTable(): IInvActivityResult
    {
        $headers = [
            'id',
            'activity_hash',
            'lead_uid',
            'type',
            'open_time',
            'close_time',
            'pair',
            'open_price',
            'close_price',
            'amount',
            'direction',
            'result',
        ];

        $rows = $this->invActivityRepo->getAll()->mapEach(fn(DbInvActivityDto $a) => [
            'id' => $a->id,
            'activity_hash' => $a->activity_hash,
            'lead_uid' => $a->lead_uid,
            'type' => $a->type,
            'open_time' => $a->open_time,
            'close_time' => $a->close_time ?? '—',
            'pair' => $a->pair,
            'open_price' => $a->open_price,
            'close_price' => $a->close_price ?? '—',
            'amount' => $a->amount,
            'direction' => $a->direction,
            'result' => $a->result ?? '—',
        ])->getArray();

        $input = new TableRenderInput(
            header: $headers,
            rows: $rows,
            attributes: ['id' => 'inv-activity-table-1', 'data-module' => 'inv-activities'],
            classes: ['base-table'],
            hrefButton: '/page/activity-edit',
            hrefButtonDel: '/',
            attributesWrapper: ['table-r-id' => 'inv-activity-table-1'],
            allowedColumns: $headers,
            renameMap: [
                'id' => 'ID',
                'activity_hash' => 'Хеш сделки',
                'lead_uid' => 'UID лида',
                'type' => 'Тип', // Открыта/закрыта
                'open_time' => 'Время открытия',
                'close_time' => 'Время закрытия',
                'pair' => 'Пара',
                'open_price' => 'Цена открытия',
                'close_price' => 'Цена закрытия',
                'amount' => 'Объём',
                'direction' => 'Направление', // long/short
                'result' => 'Результат',
            ],
        );

        $transformers = [
            new TextInputTransform(['pair', 'type', 'direction']),
        ];

        $tableFacade = new TableFacade(new TypedTableTransformer($transformers), new TableDecorator());

        return InvActivityResult::success($tableFacade->renderFilteredTable($input)->asHtml());
    }

    /**
     * @param array<string,mixed> $data
     */
    public function createActivity(array $data): IInvActivityResult
    {
        $dbDtoResult = $this->manageInvActivity->create(InvActivityMapper::fromArrayToInput($data));
        if ($dbDtoResult->isSuccess()) {
            return $dbDtoResult;
        }

        return InvActivityResult::failure($dbDtoResult->getError() ?? new \RuntimeException("Ошибка при создании активности"));
    }

    /**
     * @param array<string,mixed> $params
     */
    public function getActivityData(array $params): IInvActivityResult
    {
        // $uid = isset($params['id']) ? (int) $params['id']
        //                         : (isset($params['uid']) ? (int) $params['uid'] : 0);

        $leads = $this->invLeadRepo->getAll()->mapEach(function (DbInvLeadDto $invLead) {
            return [
                'value' => $invLead->uid,
                'text' => $invLead->contact . ' :: ' . $invLead->fullName,
            ];
        })->getArray();
        $types = [
            [
                'value' => DealType::ACTIVE->value,
                'text' => "Открытый",
            ],
            [
                'value' => DealType::CLOSED->value,
                'text' => 'Закрытый',
            ],
        ];

        $directions = [
            [
                'value' => DealDirection::LONG->value,
                'text' => "Long",
            ],
            [
                'value' => DealDirection::SHORT->value,
                'text' => 'Short',
            ],
        ];
        return InvActivityResult::success([
            'lead_uid' => $leads,
            'type' => $types,
            'direction' => $directions,
        ]);
    }

    /**
     * @param array<string,mixed> $params
     */
    public function getBalanceData(array $params): IInvBalanceResult
    {
        $uid = isset($params['id']) ? (int) $params['id']
            : (isset($params['uid']) ? (int) $params['uid'] : 0);

        $balanceRes = $this->invBalanceRepo->getByLeadUid((string)$uid);
        if ($balanceRes->isEmpty()) {
            return InvBalanceResult::success([
                'lead_uid' => $uid,
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
            'lead_uid' => $uid,
            'current' => $balanceRes->getCurrent(),
            'deposit' => $balanceRes->getDeposit(),
            'potential' => $balanceRes->getPotential(),
            'active' => $balanceRes->getActive(),
        ]);
    }
}
