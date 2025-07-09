<?php

namespace crm\src\components\LeadManagement\_common\mappers;

use crm\src\components\LeadManagement\_entities\Lead;
use crm\src\components\LeadManagement\_common\DTOs\SourceDto;
use crm\src\components\LeadManagement\_common\DTOs\StatusDto;
use crm\src\components\LeadManagement\_common\DTOs\LeadCommentDto;
use crm\src\components\LeadManagement\_common\DTOs\AccountManagerDto;

class LeadCommentDtoMapper
{
    /**
     * Преобразует сущность Lead в LeadCommentDto
     */
    public static function fromEntity(Lead $lead): LeadCommentDto
    {
        return new LeadCommentDto(
            id: $lead->id,
            fullName: $lead->fullName,
            contact: $lead->contact,
            address: $lead->address,
            sourceId: $lead->source?->id,
            sourceTitle: $lead->source?->title ?? '',
            statusId: $lead->status?->id,
            statusTitle: $lead->status?->title ?? '',
            accountManagerId: $lead->accountManager?->id,
            accountManagerLogin: $lead->accountManager?->login ?? '',
            groupName: $lead->groupName,
        );
    }

    /**
     * Преобразует массив данных (например, из формы) в LeadCommentDto
     */
    public static function fromArray(array $data): LeadCommentDto
    {
        return new LeadCommentDto(
            id: isset($data['id']) && $data['id'] !== '' ? (int)$data['id'] : null,
            fullName: (string)($data['fullName'] ?? ''),
            contact: (string)($data['contact'] ?? ''),
            address: (string)($data['address'] ?? ''),
            sourceId: isset($data['sourceId']) && $data['sourceId'] !== '' ? (int)$data['sourceId'] : null,
            sourceTitle: (string)($data['sourceTitle'] ?? ''),
            statusId: isset($data['statusId']) && $data['statusId'] !== '' ? (int)$data['statusId'] : null,
            statusTitle: (string)($data['statusTitle'] ?? ''),
            accountManagerId: isset($data['accountManagerId']) && $data['accountManagerId'] !== '' ? (int)$data['accountManagerId'] : null,
            accountManagerLogin: (string)($data['accountManagerLogin'] ?? ''),
            groupName: isset($data['groupName']) ? (string)$data['groupName'] : null,
        );
    }

    /**
     * Преобразует LeadCommentDto в сущность Lead.
     */
    public static function toEntity(LeadCommentDto $dto): Lead
    {
        return new Lead(
            fullName: $dto->fullName,
            contact: $dto->contact,
            address: $dto->address,
            source: $dto->sourceId !== null
                ? new SourceDto($dto->sourceId, $dto->sourceTitle)
                : null,
            status: $dto->statusId !== null
                ? new StatusDto($dto->statusId, $dto->statusTitle)
                : null,
            accountManager: $dto->accountManagerId !== null
                ? new AccountManagerDto($dto->accountManagerId, $dto->accountManagerLogin)
                : null,
            createdAt: null, // В LeadCommentDto нет createdAt — оставляем null, если нужно — добавьте в DTO
            id: $dto->id,
            groupName: $dto->groupName,
        );
    }
}
