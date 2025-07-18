<?php

namespace crm\src\Investments\InvSource;

use crm\src\services\TableRenderer\TableFacade;
use crm\src\services\TableRenderer\TableDecorator;
use crm\src\services\TableRenderer\TableRenderInput;
use crm\src\services\TableRenderer\TypedTableTransformer;
use crm\src\Investments\InvSource\_common\DTOs\DbInvSourceDto;
use crm\src\Investments\InvSource\_common\adapters\InvSourceResult;
use crm\src\services\TableRenderer\typesTransform\TextInputTransform;
use crm\src\Investments\InvSource\_common\interfaces\IInvSourceResult;
use crm\src\Investments\InvSource\_common\interfaces\IInvSourceRepository;

class InvSourceTableRenderer
{
    public function __construct(private IInvSourceRepository $invSourceRepo)
    {
    }

    public function getBaseTable(): IInvSourceResult
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
}
