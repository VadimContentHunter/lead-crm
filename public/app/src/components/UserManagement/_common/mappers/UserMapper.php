<?php

namespace crm\src\components\UserManagement\_common\mappers;

use crm\src\components\UserManagement\_entities\User;

class UserMapper
{
    /**
     * Преобразует массив данных в объект User или возвращает null при ошибке.
     *
     * @param  array<string, mixed> $data
     * @return User|null
     */
    public static function fromArray(array $data): ?User
    {
        if (!isset($data['login'], $data['password_hash'])) {
            return null;
        }

        return new User(
            login: (string) $data['login'],
            passwordHash: (string) $data['password_hash'],
            id: isset($data['id']) ? (int) $data['id'] : null
        );
    }

    /**
     * Преобразует объект User в массив данных.
     *
     * @return array<string, mixed>
     */
    public static function toArray(User $user): array
    {
        return [
            'id' => $user->id,
            'login' => $user->login,
            'password_hash' => $user->passwordHash,
        ];
    }

    /**
     * Преобразует объект User в массив, исключая пустые значения (null, '', 0).
     *
     * @return array<string, mixed>
     */
    public static function toNonEmptyArray(User $user): array
    {
        $data = self::toArray($user);

        return array_filter(
            $data,
            fn($value) => !($value === null || $value === '' || $value === 0),
            ARRAY_FILTER_USE_BOTH
        );
    }
}
