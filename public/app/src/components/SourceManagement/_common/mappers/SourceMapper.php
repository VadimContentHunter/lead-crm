<?php

namespace crm\src\components\SourceManagement\_common\mappers;

use crm\src\components\SourceManagement\_entities\Source;

class SourceMapper
{
    /**
     * Преобразует массив данных в объект Source.
     *
     * @param  array<string, mixed> $data
     * @return Source|null
     */
    public static function fromArray(array $data): ?Source
    {
        if (!isset($data['title'])) {
            return null;
        }

        return new Source(
            title: (string) $data['title'],
            id: isset($data['id']) ? (int) $data['id'] : null
        );
    }

    /**
     * Преобразует объект Source в массив.
     *
     * @param  Source $source
     * @return array<string, mixed>
     */
    public static function toArray(Source $source): array
    {
        return [
            'id' => $source->id,
            'title' => $source->title
        ];
    }
}
