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

    /**
     * @param array<string,mixed> $data
     */
    public function createInvSource(array $data): IInvSourceResult
    {
        return $this->manageInvSource->create(InvSourceMapper::fromArrayToInput($data));
    }

    /**
     * @param array<string,mixed> $data
     */
    public function createInvStatus(array $data): IInvStatusResult
    {
        return $this->manageInvStatus->create(InvStatusMapper::fromArrayToInput($data));
    }

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
                    ? ['value' => $item->code, 'text' => $item->label]
                    : null
            )->getArray();

            $sources = $this->invSourceRepo->getAll()->mapEach(
                fn($item) => $item instanceof DbInvSourceDto
                ? ['value' => $item->code, 'text' => $item->label]
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

        $tableFacade = new TableFacade(new TableTransformer(),  new TableDecorator());
        $a = $tableFacade->renderFilteredTable($input)->asHtml();
        return InvSourceResult::success($a);
    }
}
