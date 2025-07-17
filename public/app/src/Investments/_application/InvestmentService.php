<?php

namespace crm\src\Investments\_application;

use crm\src\Investments\InvLead\ManageInvLead;
use crm\src\services\TableRenderer\TableFacade;
use crm\src\Investments\InvSource\ManageInvSource;
use crm\src\Investments\InvStatus\ManageInvStatus;
use crm\src\services\TableRenderer\TableDecorator;
use crm\src\services\TableRenderer\TableRenderInput;
use crm\src\services\TableRenderer\TableTransformer;
use crm\src\Investments\InvSource\_entities\InvSource;
use crm\src\Investments\InvStatus\_entities\InvStatus;
use crm\src\Investments\InvLead\_entities\SimpleInvLead;
use crm\src\services\TableRenderer\TypedTableTransformer;
use crm\src\Investments\InvLead\_common\DTOs\DbInvLeadDto;
use crm\src\Investments\_application\adapters\InvestResult;
use crm\src\Investments\_application\interfaces\IInvestResult;
use crm\src\Investments\InvLead\_common\mappers\InvLeadMapper;
use crm\src\Investments\InvSource\_common\DTOs\DbInvSourceDto;
use crm\src\Investments\InvStatus\_common\DTOs\DbInvStatusDto;
use crm\src\Investments\InvLead\_common\adapters\InvLeadResult;
use crm\src\_common\adapters\Investments\SourceValidatorAdapter;
use crm\src\_common\adapters\Investments\StatusValidatorAdapter;
use crm\src\_common\adapters\Investments\InvLeadValidatorAdapter;
use crm\src\Investments\InvLead\_common\interfaces\IInvLeadResult;
use crm\src\Investments\InvSource\_common\mappers\InvSourceMapper;
use crm\src\Investments\InvStatus\_common\mappers\InvStatusMapper;
use crm\src\Investments\InvSource\_common\adapters\InvSourceResult;
use crm\src\Investments\InvStatus\_common\adapters\InvStatusResult;
use crm\src\services\TableRenderer\typesTransform\TextInputTransform;
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

    // === CRUD: Лиды ===

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

    /**
     * @param callable|null $accountManagerFetcher Функция (int $id): string|null
     */
    public function getInvLeadTable(?callable $accountManagerFetcher = null): IInvLeadResult
    {
        $headers = [
            'uid',
            'created_at',
            'contact',
            'phone',
            'email',
            'full_name',
            'account_manager',
            'source',
            'status',
        ];

        // Получаем лиды и превращаем в массив
        $rows = $this->invLeadRepo->getAll()->mapEach(function (DbInvLeadDto $lead) use ($accountManagerFetcher) {

            $managerLabel = '—';
            if (is_callable($accountManagerFetcher) && $lead->accountManagerId !== null) {
                $managerLabel = $accountManagerFetcher($lead->accountManagerId) ?? '—';
            }

            $sourceLabel = '—';
            $sourceResult = $this->invSourceRepo->getById($lead->sourceId ?? 0);
            if ($sourceResult->isSuccess() && $sourceResult instanceof IInvSourceResult) {
                $sourceLabel = $sourceResult->getData()?->label; //DbInvSourceDto
            }

            $statusLabel = '—';
            $statusResult = $this->invStatusRepo->getById($lead->statusId ?? 0);
            if ($statusResult->isSuccess() && $statusResult instanceof IInvStatusResult) {
                $statusLabel = $statusResult->getData()?->label; //DbInvStatusDto
            }

            return [
                'uid' => $lead->uid,
                'created_at' => $lead->createdAt ?? '—',
                'contact' => $lead->contact,
                'phone' => $lead->phone,
                'email' => $lead->email,
                'full_name' => $lead->fullName,
                'account_manager' => $managerLabel ?? '—',
                'source' => $sourceLabel ?? '—',
                'status' => $statusLabel ?? '—',
            ];
        })->getArray();

        $input = new TableRenderInput(
            header: $headers,
            rows: $rows,
            attributes: ['id' => 'inv-lead-table-1', 'data-module' => 'inv-leads'],
            classes: ['base-table'],
            hrefButton: '/page/lead-edit',
            hrefButtonDel: '/',
            attributesWrapper: ['table-r-id' => 'inv-lead-table-1'],
            allowedColumns: $headers,
            renameMap: [
                'uid' => 'UID',
                'created_at' => 'Создан',
                'contact' => 'Контакт',
                'phone' => 'Телефон',
                'email' => 'Email',
                'full_name' => 'Полное имя',
                'account_manager' => 'Менеджер',
                'source' => 'Источник',
                'status' => 'Статус',
            ],
        );

        $transformers = [
            new TextInputTransform(['contact', 'phone', 'email', 'full_name']),
        ];

        $tableFacade = new TableFacade(new TypedTableTransformer($transformers), new TableDecorator());

        return InvLeadResult::success($tableFacade->renderFilteredTable($input)->asHtml());
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
        $headers = $this->invSourceRepo->getColumnNames()->getArray();
        $rows = $this->invSourceRepo->getAll()->mapEach(function (DbInvSourceDto $source) {
            return [
                'id' => $source->id,
                'code' => $source->code,
                'label' => $source->label,
            ];
        })->getArray();

        $input = new TableRenderInput(
            header: $headers,
            rows: $rows,
            attributes: ['id' => 'inv-source-table-1', 'data-module' => 'inv-sources'],
            classes: ['base-table'],
            hrefButton: '/page/source-edit',
            hrefButtonDel: '/',
            attributesWrapper: [
                'table-r-id' => 'inv-source-table-1'
            ],
            allowedColumns: [
                'id',
                'code',
                'label',
            ],
            renameMap: [],
        );

        $typeTransformers = [
            new TextInputTransform(['code', 'label']),
        ];
        $tableFacade = new TableFacade(new TypedTableTransformer($typeTransformers),  new TableDecorator());
        return InvSourceResult::success($tableFacade->renderFilteredTable($input)->asHtml());
    }

    /**
     * @param array<string,mixed> $data
     */
    public function updateSource(array $data): IInvSourceResult
    {
        $data['id'] = isset($data['id']) ? (int) $data['id']
                        : (isset($data['data-row-id']) ? (int) $data['data-row-id'] : null);
        $data['code'] = $data['name'] === "code" ? $data['value'] : null;
        $data['label'] = $data['name'] === "label" ? $data['value'] : null;

        return $this->manageInvSource->updateById(InvSourceMapper::fromArrayToInput($data));
    }

    /**
     * @param array<string,mixed> $data
     */
    public function deleteSource(array $data): IInvSourceResult
    {
        $id = isset($data['id']) ? (int) $data['id']
                            : (isset($data['rowId']) ? (int) $data['rowId'] : null);

        $oldData = $this->invSourceRepo->getById($id ?? 0);
        $resultDelete = $this->manageInvSource->deleteById($id ?? 0);
        if ($resultDelete->isSuccess()) {
            $data = $oldData->getData() instanceof DbInvSourceDto
                        ? InvSourceMapper::fromDbToEntity($oldData->getData())
                        : null;
            return InvSourceResult::success($data);
        }

        return InvSourceResult::failure($resultDelete->getError() ?? new \RuntimeException("Ошибка при удалении источника"));
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
        $data['id'] = $data['id'] ?? $data['data-row-id'] ?? null;
        $data['code'] = $data['name'] === "code" ? $data['value'] : null;
        $data['label'] = $data['name'] === "label" ? $data['value'] : null;

        return $this->manageInvStatus->updateById(InvStatusMapper::fromArrayToInput($data));
    }

    /**
     * @param array<string,mixed> $data
     */
    public function deleteStatus(array $data): IInvStatusResult
    {
        $id = $data['id'] ?? $data['rowId'] ?? null;
        $oldData = $this->invStatusRepo->getById($id);
        $resultDelete = $this->manageInvStatus->deleteById($id);

        if ($resultDelete->isSuccess()) {
            $data = $oldData->getData() instanceof DbInvStatusDto
                ? InvStatusMapper::fromDbToEntity($oldData->getData())
                : null;
            return InvStatusResult::success($data);
        }

        return InvStatusResult::failure($resultDelete->getError() ?? new \RuntimeException("Ошибка при удалении статуса"));
    }

    public function getStatusTable(): IInvStatusResult
    {
        $headers = $this->invStatusRepo->getColumnNames()->getArray();
        $rows = $this->invStatusRepo->getAll()->mapEach(fn(DbInvStatusDto $s) => [
            'id' => $s->id,
            'code' => $s->code,
            'label' => $s->label,
        ])->getArray();

        $input = new TableRenderInput(
            header: $headers,
            rows: $rows,
            attributes: ['id' => 'inv-status-table-1', 'data-module' => 'inv-statuses'],
            classes: ['base-table'],
            hrefButton: '/page/status-edit',
            hrefButtonDel: '/',
            attributesWrapper: ['table-r-id' => 'inv-status-table-1'],
            allowedColumns: ['id', 'code', 'label'],
            renameMap: [],
        );

        $tableFacade = new TableFacade(new TypedTableTransformer([new TextInputTransform(['code', 'label'])]), new TableDecorator());
        return InvStatusResult::success($tableFacade->renderFilteredTable($input)->asHtml());
    }

    // === Формы ===

    /**
     * Возвращает данные для формы создания лида.
     *
     * @param array<string,mixed> $params
     * @param array<string,mixed> $extraData Данные, которые нужно добавить/переопределить в итоговом массиве
     */
    public function getFormCreateData(array $params, array $extraData = []): IInvestResult
    {
        $id = $params['id'] ?? 0;

        if (filter_var($id, FILTER_VALIDATE_INT) === false) {
            return InvestResult::failure(new \RuntimeException('Неверный идентификатор'));
        }

        if ((int)$id === 0) {
            $statuses = $this->invStatusRepo->getAll()->mapEach(
                fn($item) => $item instanceof DbInvStatusDto
                    ? ['value' => $item->id, 'text' => $item->label]
                    : null
            )->getArray();

            $sources = $this->invSourceRepo->getAll()->mapEach(
                fn($item) => $item instanceof DbInvSourceDto
                ? ['value' => $item->id, 'text' => $item->label]
                : null
            )->getArray();

            // Добавляем заглушки с selected => true
            array_unshift($statuses, ['value' => '', 'text' => '— Выберите статус —', 'selected' => true]);
            array_unshift($sources,  ['value' => '', 'text' => '— Выберите источник —', 'selected' => true]);

            // Базовые данные
            $data = [
                'status_id' => $statuses,
                'source_id' => $sources,
            ];

            // Объединение с внешними данными
            $data = array_merge($data, $extraData);

            return InvestResult::success($data);
        }

        return InvestResult::success();
    }
}
