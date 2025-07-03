<?php

namespace crm\src\components\LeadManagement\_common\mappers;

use crm\src\components\LeadManagement\_entities\Lead;
use crm\src\components\LeadManagement\_common\DTOs\SourceDto;
use crm\src\components\LeadManagement\_common\DTOs\StatusDto;
use crm\src\components\LeadManagement\_common\DTOs\AccountManagerDto;
use DateTime;

class LeadMapper
{
    /**
     * Преобразует массив данных из БД в сущность Lead.
     *
     * @param  array<string, mixed> $data
     * @return Lead
     */
    public static function fromArray(array $data): Lead
    {
        return new Lead(
            fullName: (string)($data['full_name'] ?? ''),
            contact: (string)($data['contact'] ?? ''),
            address: (string)($data['address'] ?? ''),
            source: isset($data['source_id'])
                ? new SourceDto((int)$data['source_id'], '') // Можно заменить на SourceDtoMapper::fromArray() позже
                : null,
            status: isset($data['status_id'])
                ? new StatusDto((int)$data['status_id'], '')
                : null,
            accountManager: isset($data['account_manager_id'])
                ? new AccountManagerDto((int)$data['account_manager_id'], '')
                : null,
            createdAt: isset($data['created_at'])
                ? new DateTime($data['created_at'])
                : null,
            id: isset($data['id']) ? (int)$data['id'] : null,
        );
    }

    /**
     * Преобразует сущность Lead в массив для БД.
     *
     * @param  Lead $lead
     * @return array<string, mixed>
     */
    public static function toArray(Lead $lead): array
    {
        return [
            'id' => $lead->id,
            'full_name' => $lead->fullName,
            'contact' => $lead->contact,
            'address' => $lead->address,
            'source_id' => $lead->source?->id,
            'status_id' => $lead->status?->id,
            'account_manager_id' => $lead->accountManager?->id,
            'created_at' => $lead->createdAt?->format('Y-m-d H:i:s'),
        ];
    }
}
