<?php

namespace crm\src\Investments\InvLead;

use crm\src\services\TableRenderer\TableFacade;
use crm\src\services\TableRenderer\TableDecorator;
use crm\src\services\TableRenderer\TableRenderInput;
use crm\src\services\TableRenderer\TypedTableTransformer;
use crm\src\Investments\InvLead\_common\DTOs\DbInvLeadDto;
use crm\src\Investments\InvLead\_common\adapters\InvLeadResult;
use crm\src\Investments\InvLead\_common\interfaces\IInvLeadResult;
use crm\src\services\TableRenderer\typesTransform\TextInputTransform;
use crm\src\Investments\InvLead\_common\interfaces\IInvLeadRepository;
use crm\src\Investments\InvSource\_common\interfaces\IInvSourceResult;
use crm\src\Investments\InvStatus\_common\interfaces\IInvStatusResult;
use crm\src\Investments\InvSource\_common\interfaces\IInvSourceRepository;
use crm\src\Investments\InvStatus\_common\interfaces\IInvStatusRepository;
use crm\src\Investments\InvLead\_common\interfaces\IInvAccountManagerRepository;

class InvLeadTableRenderer
{
    public function __construct(
        private IInvLeadRepository $invLeadRepo,
        private IInvSourceRepository $invSourceRepo,
        private IInvStatusRepository $invStatusRepo,
        private IInvAccountManagerRepository $accountManagerRepo
    ) {
    }

    public function getBaseTable(): IInvLeadResult
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
        $rows = $this->invLeadRepo->getAll()->mapEach(function (DbInvLeadDto $lead) {

            $accountManagerResult = $this->accountManagerRepo->getById($lead->accountManagerId ?? 0);
            $managerLabel = $accountManagerResult?->login ?? '—';

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
            hrefButton: '/invest/lead-edit',
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
}
