<?php

namespace crm\src\Investments\InvActivity;

use crm\src\Investments\InvActivity\_entities\DealType;
use crm\src\Investments\InvLead\_common\DTOs\DbInvLeadDto;
use crm\src\Investments\InvActivity\_entities\DealDirection;
use crm\src\Investments\InvLead\_common\interfaces\IInvLeadRepository;
use crm\src\Investments\InvActivity\_common\adapters\InvActivityResult;
use crm\src\Investments\InvActivity\_common\interfaces\IInvActivityResult;
use crm\src\Investments\InvSource\_common\interfaces\IInvSourceRepository;
use crm\src\Investments\InvStatus\_common\interfaces\IInvStatusRepository;
use crm\src\Investments\InvLead\_common\interfaces\IInvAccountManagerRepository;

final class RenderInvActivityForm
{
    public function __construct(private IInvLeadRepository $invLeadRepo)
    {
    }

    /**
     * @param array<string,mixed> $params
     */
    public function getFormCreateData(array $params): IInvActivityResult
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
}
