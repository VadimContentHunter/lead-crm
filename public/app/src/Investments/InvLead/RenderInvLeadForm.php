<?php

namespace crm\src\Investments\InvLead;

use crm\src\Investments\InvLead\_entities\SimpleInvLead;
use crm\src\Investments\InvLead\_common\DTOs\DbInvLeadDto;
use crm\src\Investments\InvLead\_common\mappers\InvLeadMapper;
use crm\src\Investments\InvSource\_common\DTOs\DbInvSourceDto;
use crm\src\Investments\InvStatus\_common\DTOs\DbInvStatusDto;
use crm\src\Investments\InvLead\_common\adapters\InvLeadResult;
use crm\src\Investments\InvLead\_common\interfaces\IInvLeadResult;
use crm\src\Investments\InvLead\_common\interfaces\IInvLeadRepository;
use crm\src\Investments\InvSource\_common\interfaces\IInvSourceRepository;
use crm\src\Investments\InvStatus\_common\interfaces\IInvStatusRepository;
use crm\src\Investments\InvLead\_common\interfaces\IInvAccountManagerRepository;

final class RenderInvLeadForm
{
    public function __construct(
        private IInvLeadRepository $invLeadRepo,
        private IInvStatusRepository $invStatusRepo,
        private IInvSourceRepository $invSourceRepo,
        private IInvAccountManagerRepository $accountManagerRepo
    ) {
    }

    /**
     * Возвращает данные для формы создания лида.
     *
     * @param array<string,mixed> $params
     * @param array<string,mixed> $extraData Данные,
     *                                       которые
     *                                       нужно
     *                                       добавить/переопределить
     *                                       в итоговом
     *                                       массиве
     */
    public function getFormCreateData(
        array $params,
        array $extraData = [],
    ): IInvLeadResult {
        $uid = isset($params['id']) ? (int) $params['id']
                                : (isset($params['uid']) ? (int) $params['uid'] : 0);

        $managersRes = $this->accountManagerRepo->getAll();
        // === Новый лид
        if ($uid === 0) {
            // accountManager как массив по колбэку
            $formattedManagers = array_map(function ($user) {
                return [
                    'value'    => $user->id,
                    'text'     => $user->login,
                    'selected' => false
                ];
            }, $managersRes);


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

            array_unshift($statuses, ['value' => '', 'text' => '— Выберите статус —', 'selected' => true]);
            array_unshift($sources,  ['value' => '', 'text' => '— Выберите источник —', 'selected' => true]);
            array_unshift($formattedManagers, ['value' => '', 'text' => '— Выберите менеджера —', 'selected' => true]);

            $data = [
                'status_id' => $statuses,
                'source_id' => $sources,
                'account_manager_id' => $formattedManagers
            ];

            return InvLeadResult::success(array_merge($data, $extraData));
        }

        // === Существующий лид
        $lead = $this->invLeadRepo->getById($uid)->getData();
        if (!($lead instanceof DbInvLeadDto) && !($lead instanceof SimpleInvLead)) {
            return InvLeadResult::failure(new \RuntimeException('Неверный идентификатор'));
        }

        if ($lead instanceof DbInvLeadDto) {
            $lead = InvLeadMapper::fromDbToEntity($lead);
        }


        $statuses = $this->invStatusRepo->getAll()->mapEach(
            function (DbInvStatusDto $status) use ($lead) {
                return [
                    'value' => $status->id,
                    'text' => $status->label,
                    'selected' => $status->id === $lead->status?->id
                ];
            }
        )->getArray();

        $sources = $this->invSourceRepo->getAll()->mapEach(
            function (DbInvSourceDto $source) use ($lead) {
                return [
                    'value' => $source->id,
                    'text' => $source->label,
                    'selected' => $source->id === $lead->source?->id
                ];
            }
        )->getArray();

        // accountManager как массив по колбэку
        $formattedManagers = array_map(function ($user) use ($lead) {
            return [
                'value'    => $user->id,
                'text'     => $user->login,
                'selected' => $user->id === $lead->accountManager?->id
            ];
        }, $managersRes);

        array_unshift($statuses, ['value' => '', 'text' => '— Выберите статус —', 'selected' => false]);
        array_unshift($sources,  ['value' => '', 'text' => '— Выберите источник —', 'selected' => false]);
        array_unshift($formattedManagers, ['value' => '', 'text' => '— Выберите менеджера —', 'selected' => false]);

        $data = [
            'lead_uid' => $lead->uid,
            'full_name' => $lead->fullName,
            'contact' => $lead->contact,
            'phone' => $lead->phone,
            'email' => $lead->email,
            'account_manager_id' => $formattedManagers,
            'status_id' => $statuses,
            'source_id' => $sources,
        ];

        return InvLeadResult::success(array_merge($data, $extraData));
    }
}
