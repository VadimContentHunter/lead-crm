<?php

namespace crm\src\components\LeadManagement\_common\mappers;

use crm\src\components\LeadManagement\_common\DTOs\LeadFilterDto;

class LeadFilterMapper
{
    /**
     * Преобразует массив данных в LeadFilterDto.
     *
     * @param  array<string, mixed> $data
     * @return LeadFilterDto
     */
    public static function fromArray(array $data): LeadFilterDto
    {
        return new LeadFilterDto(
            search: $data['search'] ?? null,
            manager: $data['manager'] ?? $data['managerId'] ?? null,
            status: $data['status'] ?? $data['statusId'] ?? null,
            source: $data['source'] ?? $data['sourceId'] ?? null,
            potentialMin: isset($data['potentialMin']) && $data['potentialMin'] !== '' ? (float)$data['potentialMin'] : null,
            balanceMin: isset($data['balanceMin']) && $data['balanceMin'] !== '' ? (float)$data['balanceMin'] : null,
            drainMin: isset($data['drainMin']) && $data['drainMin'] !== '' ? (float)$data['drainMin'] : null,
            sort: $data['sort'] ?? '',
            sortDir: $data['sortDir'] ?? $data['dir'] ?? 'asc',
            groupName: $data['groupName'] ?? null,
        );
    }


    /**
     * Преобразует LeadFilterDto в массив.
     *
     * @param  LeadFilterDto $dto
     * @return array<string, mixed>
     */
    public static function toArray(LeadFilterDto $dto): array
    {
        return [
            'search' => $dto->search,
            'manager' => $dto->manager,
            'status' => $dto->status,
            'source' => $dto->source,
            'potentialMin' => $dto->potentialMin,
            'balanceMin' => $dto->balanceMin,
            'drainMin' => $dto->drainMin,
            'sort' => $dto->sort,
            'sortDir' => $dto->sortDir,
            'groupName' => $dto->groupName,
        ];
    }
}
