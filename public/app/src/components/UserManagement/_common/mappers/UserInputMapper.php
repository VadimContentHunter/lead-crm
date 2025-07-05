<?php

namespace crm\src\components\UserManagement\_common\mappers;

use crm\src\components\UserManagement\_common\DTOs\UserInputDto;

class UserInputMapper
{
    /**
     * Преобразует массив данных в UserInputDto.
     *
     * @param  array<string,mixed> $data
     * @return UserInputDto
     */
    public static function fromArray(array $data): UserInputDto
    {
        return new UserInputDto(
            login: (string) ($data['login'] ?? ''),
            plainPassword: (string) ($data['password'] ?? ''),
            confirmPassword: (string) ($data['password_confirm'] ?? ''),
            id: isset($data['id']) ? (int) $data['id'] : 0
        );
    }

    /**
     * Преобразует UserInputDto в массив, исключая пустые значения (null, '', 0).
     *
     * @return array<string,mixed>
     */
    public static function toNonEmptyArray(UserInputDto $dto): array
    {
        return array_filter(
            [
                'id' => $dto->id,
                'login' => $dto->login,
                'plain_password' => $dto->plainPassword,
                'confirm_password' => $dto->confirmPassword,
            ],
            fn($value) => !($value === '' || $value === 0)
        );
    }
}
