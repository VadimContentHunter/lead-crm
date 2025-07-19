<?php

namespace crm\src\Investments\InvStatus;

use crm\src\services\TableRenderer\TableFacade;
use crm\src\services\TableRenderer\TableDecorator;
use crm\src\services\TableRenderer\TableRenderInput;
use crm\src\services\TableRenderer\TypedTableTransformer;
use crm\src\Investments\InvStatus\_common\DTOs\DbInvStatusDto;
use crm\src\Investments\InvStatus\_common\adapters\InvStatusResult;
use crm\src\services\TableRenderer\typesTransform\TextInputTransform;
use crm\src\Investments\InvStatus\_common\interfaces\IInvStatusResult;
use crm\src\Investments\InvStatus\_common\interfaces\IInvStatusRepository;

class InvStatusTableRenderer
{
    public function __construct(private IInvStatusRepository $invStatusRepo)
    {
    }

    public function getBaseTable(): IInvStatusResult
    {
        $headers = $this->invStatusRepo->getColumnNames()->getArray();
        $rows = $this->invStatusRepo->getAll()->mapEach(function (DbInvStatusDto $status) {
            return [
                'id' => $status->id,
                'code' => $status->code,
                'label' => $status->label,
            ];
        })->getArray();

        $input = new TableRenderInput(
            header: $headers,
            rows: $rows,
            attributes: ['id' => 'inv-status-table-1', 'data-module' => 'inv-statuses'],
            classes: ['base-table'],
            hrefButton: '/page/status-edit',
            hrefButtonDel: '/',
            attributesWrapper: [
                'table-r-id' => 'inv-status-table-1'
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

        $tableFacade = new TableFacade(new TypedTableTransformer($typeTransformers), new TableDecorator());

        return InvStatusResult::success($tableFacade->renderFilteredTable($input)->asHtml());
    }
}
