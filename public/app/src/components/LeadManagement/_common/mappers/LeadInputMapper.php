<?php

namespace crm\src\components\LeadManagement\_common\mappers;

use crm\src\components\LeadManagement\_entities\Lead;
use crm\src\components\LeadManagement\_common\DTOs\LeadInputDto;

class LeadInputMapper
{
    /**
     * Преобразует массив данных в DTO LeadInputDto.
     *
     * @param  array<string, mixed> $data
     * @return LeadInputDto|null
     */
    public static function fromArray(array $data): ?LeadInputDto
    {
        if (!isset($data['fullName'], $data['contact'])) {
            return null;
        }

        return new LeadInputDto(
            id: isset($data['id']) ? (int) $data['id'] : null,
            fullName: (string) $data['fullName'],
            address: isset($data['address']) ? (string) $data['address'] : '',
            contact: (string) $data['contact'],
            sourceId: isset($data['sourceId']) ? (int) $data['sourceId'] : null,
            statusId: isset($data['statusId']) ? (int) $data['statusId'] : null,
            accountManagerId: isset($data['accountManagerId']) ? (int) $data['accountManagerId'] : null,
            groupName: isset($data['groupName']) ? (string) $data['groupName'] : null
        );
    }

    /**
     * Преобразует DTO LeadInputDto в массив.
     *
     * @param  LeadInputDto $dto
     * @return array<string, mixed>
     */
    public static function toArray(LeadInputDto $dto): array
    {
        return [
            'id' => $dto->id,
            'fullName' => $dto->fullName,
            'address' => $dto->address,
            'contact' => $dto->contact,
            'sourceId' => $dto->sourceId,
            'statusId' => $dto->statusId,
            'accountManagerId' => $dto->accountManagerId,
            'groupName' => $dto->groupName
        ];
    }

     /**
      * Преобразует сущность Lead в LeadInputDto.
      *
      * @param  Lead $lead
      * @return LeadInputDto
      */
    public static function fromEntity(Lead $lead): LeadInputDto
    {
        return new LeadInputDto(
            id: $lead->id,
            fullName: $lead->fullName,
            address: $lead->address,
            contact: $lead->contact,
            sourceId: $lead->source?->id,
            statusId: $lead->status?->id,
            accountManagerId: $lead->accountManager?->id,
            groupName: $lead->groupName
        );
    }
}
