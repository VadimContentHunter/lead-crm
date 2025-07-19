<?php

namespace crm\src\Investments\InvActivity;

use crm\src\services\TableRenderer\TableFacade;
use crm\src\services\TableRenderer\TableDecorator;
use crm\src\services\TableRenderer\TableRenderInput;
use crm\src\services\TableRenderer\TypedTableTransformer;
use crm\src\Investments\InvActivity\_common\DTOs\DbInvActivityDto;
use crm\src\services\TableRenderer\typesTransform\TextInputTransform;
use crm\src\Investments\InvActivity\_common\adapters\InvActivityResult;
use crm\src\Investments\InvActivity\_common\interfaces\IInvActivityResult;
use crm\src\Investments\InvActivity\_common\interfaces\IInvActivityRepository;

class InvActivityTableRenderer
{
    public function __construct(
        private IInvActivityRepository $invActivityRepo,
    ) {
    }

    public function getBaseTable(): IInvActivityResult
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
}
