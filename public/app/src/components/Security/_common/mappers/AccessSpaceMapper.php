<?php

namespace crm\src\components\Security\_common\mappers;

use crm\src\components\Security\_entities\AccessSpace;

class AccessSpaceMapper
{
    /**
     * Преобразует массив данных в объект AccessSpace или возвращает null при ошибке.
     *
     * @param  array<string, mixed> $data
     * @return AccessSpace|null
     */
    public static function fromArrayDb(array $data): ?AccessSpace
    {
        if (!isset($data['name']) || !is_string($data['name']) || trim($data['name']) === '') {
            return null;
        }

        return new AccessSpace(
            name: $data['name'],
            id: isset($data['id']) ? (int) $data['id'] : null,
            description: $data['description'] ?? null
        );
    }

    /**
     * Преобразует объект AccessSpace в массив данных.
     *
     * @return array<string, mixed>
     */
    public static function toArrayDb(AccessSpace $obj): array
    {
        return [
            'id' => $obj->id,
            'name' => $obj->name,
            'description' => $obj->description,
        ];
    }

    /**
     * Преобразует объект AccessSpace в массив, исключая пустые значения.
     *
     * @return array<string, mixed>
     */
    public static function toNonEmptyArray(AccessSpace $obj): array
    {
        return array_filter(
            self::toArrayDb($obj),
            fn($value) => !($value === null || $value === '' || $value === 0),
            ARRAY_FILTER_USE_BOTH
        );
    }
}
