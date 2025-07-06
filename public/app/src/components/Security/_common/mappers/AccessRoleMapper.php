<?php

namespace crm\src\components\Security\_common\mappers;

use crm\src\components\Security\_entities\AccessRole;

class AccessRoleMapper
{
    /**
     * Преобразует массив данных в объект AccessRole или возвращает null при ошибке.
     *
     * @param  array<string, mixed> $data
     * @return AccessRole|null
     */
    public static function fromArrayDb(array $data): ?AccessRole
    {
        if (!isset($data['name']) || !is_string($data['name']) || trim($data['name']) === '') {
            return null;
        }

        return new AccessRole(
            name: $data['name'],
            id: isset($data['id']) ? (int) $data['id'] : null,
            description: $data['description'] ?? null
        );
    }

    /**
     * Преобразует объект AccessRole в массив данных.
     *
     * @return array<string, mixed>
     */
    public static function toArrayDb(AccessRole $obj): array
    {
        return [
            'id' => $obj->id,
            'name' => $obj->name,
            'description' => $obj->description,
        ];
    }

    /**
     * Преобразует объект AccessRole в массив, исключая пустые значения.
     *
     * @return array<string, mixed>
     */
    public static function toNonEmptyArray(AccessRole $obj): array
    {
        return array_filter(
            self::toArrayDb($obj),
            fn($value) => !($value === null || $value === '' || $value === 0),
            ARRAY_FILTER_USE_BOTH
        );
    }
}
