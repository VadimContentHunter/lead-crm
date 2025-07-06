<?php

namespace crm\src\components\Security\_common\mappers;

use crm\src\components\Security\_entities\AccessContext;

class AccessContextMapper
{
    /**
     * Преобразует массив данных в объект AccessContext или возвращает null при ошибке.
     *
     * @param  array<string,mixed> $data
     * @return AccessContext|null
     */
    public static function fromArrayDb(array $data): ?AccessContext
    {
        if (!is_string($data['user_id']) || $data['user_id'] === "") {
            return null;
        }

        return new AccessContext(
            userId: (int) $data['user_id'],
            sessionAccessHash: (string) $data['session_access_hash'],
            id: (int) $data['id'],
            roleId: isset($data['role_id']) ? (int) $data['role_id'] : null,
            spaceId: isset($data['space_id']) ? (int) $data['space_id'] : null
        );
    }

    /**
     * Преобразует объект AccessContext в массив данных.
     *
     * @return array<string, mixed>
     */
    public static function toArrayDb(AccessContext $obj): array
    {
        return [
            'id' => $obj->id,
            'session_access_hash' => $obj->sessionAccessHash,
            'user_id' => $obj->userId,
            'role_id' => $obj->roleId,
            'space_id' => $obj->spaceId
        ];
    }

    /**
     * Преобразует объект AccessContext в массив, исключая пустые значения (null, '', 0).
     *
     * @return array<string, mixed>
     */
    public static function toNonEmptyArray(AccessContext $obj): array
    {
        $data = self::toArrayDb($obj);

        return array_filter(
            $data,
            fn($value) => !($value === null || $value === '' || $value === 0),
            ARRAY_FILTER_USE_BOTH
        );
    }
}
