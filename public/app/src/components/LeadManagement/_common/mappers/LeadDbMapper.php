<?php

namespace crm\src\components\LeadManagement\_common\mappers;

use crm\src\components\LeadManagement\_entities\Lead;
use crm\src\components\LeadManagement\_common\DTOs\LeadDbDto;
use crm\src\components\LeadManagement\_common\DTOs\LeadInputDto;

class LeadDbMapper
{
    /**
     * Преобразует массив данных из БД в LeadDbDto.
     *
     * @param  array<string, mixed> $data
     * @return LeadDbDto|null
     */
    public static function fromArray(array $data): ?LeadDbDto
    {
        if (!isset($data['full_name'], $data['contact'])) {
            return null;
        }

        return new LeadDbDto(
            id: isset($data['id']) ? (int) $data['id'] : null,
            fullName: (string) $data['full_name'],
            address: isset($data['address']) ? (string) $data['address'] : '',
            contact: (string) $data['contact'],
            sourceId: isset($data['source_id']) ? (int) $data['source_id'] : null,
            statusId: isset($data['status_id']) ? (int) $data['status_id'] : null,
            accountManagerId: isset($data['account_manager_id']) ? (int) $data['account_manager_id'] : null,
            createdAt: isset($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : null,
        );
    }

    /**
     * Преобразует LeadDbDto в массив для сохранения в БД.
     *
     * @param  LeadDbDto $dto
     * @return array<string, mixed>
     */
    public static function toArray(LeadDbDto $dto): array
    {
        return [
            'id' => $dto->id,
            'full_name' => $dto->fullName,
            'address' => $dto->address,
            'contact' => $dto->contact,
            'source_id' => $dto->sourceId,
            'status_id' => $dto->statusId,
            'account_manager_id' => $dto->accountManagerId,
            'created_at' => $dto->createdAt?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Преобразует LeadInputDto в массив для сохранения в БД (формат колонок таблицы leads).
     *
     * @param  LeadInputDto $dto
     * @return array<string, mixed>
     */
    public static function fromInputDtoToArray(LeadInputDto $dto): array
    {
        return [
            'id' => $dto->id,
            'full_name' => $dto->fullName,
            'address' => $dto->address,
            'contact' => $dto->contact,
            'source_id' => $dto->sourceId,
            'status_id' => $dto->statusId,
            'account_manager_id' => $dto->accountManagerId,
            // 'created_at' => null,
        ];
    }

    /**
     * Преобразует Lead в массив для сохранения в БД (формат колонок таблицы leads).
     *
     * @param  Lead $lead
     * @return array<string, mixed>
     */
    public static function fromEntityToArray(Lead $lead): array
    {
        return [
        'id' => $lead->id,
        'full_name' => $lead->fullName,
        'address' => $lead->address,
        'contact' => $lead->contact,
        'source_id' => $lead->source?->id,
        'status_id' => $lead->status?->id,
        'account_manager_id' => $lead->accountManager?->id,
        'created_at' => $lead->createdAt?->format('Y-m-d H:i:s'),
        ];
    }
}
