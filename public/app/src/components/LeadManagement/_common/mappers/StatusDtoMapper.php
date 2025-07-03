<?php

namespace crm\src\components\LeadManagement\_common\mappers;

use crm\src\components\LeadManagement\_common\DTOs\StatusDto;

class StatusDtoMapper
{
    /**
     * Преобразует массив данных в StatusDto.
     *
     * @param  array<string, mixed> $data
     * @return StatusDto|null
     */
    public static function fromArray(array $data): ?StatusDto
    {
        if (!isset($data['title'])) {
            return null;
        }

        return new StatusDto(
            id: isset($data['id']) ? (int)$data['id'] : null,
            title: (string)$data['title'],
        );
    }

    /**
     * Преобразует StatusDto в массив.
     *
     * @param  StatusDto $dto
     * @return array<string, mixed>
     */
    public static function toArray(StatusDto $dto): array
    {
        return [
            'id' => $dto->id,
            'title' => $dto->title,
        ];
    }
}
