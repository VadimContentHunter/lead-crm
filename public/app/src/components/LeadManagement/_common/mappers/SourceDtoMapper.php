<?php

namespace crm\src\components\LeadManagement\_common\mappers;

use crm\src\components\LeadManagement\_common\DTOs\SourceDto;

class SourceDtoMapper
{
    public static function fromArray(array $data): ?SourceDto
    {
        if (!isset($data['title'])) {
            return null;
        }

        return new SourceDto(
            id: isset($data['id']) ? (int)$data['id'] : null,
            title: (string)$data['title'],
        );
    }

    public static function toArray(SourceDto $dto): array
    {
        return [
            'id' => $dto->id,
            'title' => $dto->title
        ];
    }
}
