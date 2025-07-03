<?php

namespace crm\src\components\StatusManagement\_common\mappers;

use crm\src\components\StatusManagement\_entities\Status;

class StatusMapper
{
    /**
     * Преобразует массив данных в объект Status.
     *
     * @param  array<string, mixed> $data
     * @return Status|null
     */
    public static function fromArray(array $data): ?Status
    {
        if (!isset($data['title'])) {
            return null;
        }

        return new Status(
            title: (string) $data['title'],
            id: isset($data['id']) ? (int) $data['id'] : null
        );
    }

    /**
     * Преобразует объект Status в массив.
     *
     * @param  Status $status
     * @return array<string, mixed>
     */
    public static function toArray(Status $status): array
    {
        return [
            'id' => $status->id,
            'title' => $status->title
        ];
    }
}
