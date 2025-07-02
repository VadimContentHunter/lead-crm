<?php

namespace crm\src\components\UserManagement\_common\mappers;

use crm\src\components\UserManagement\_common\DTOs\UserInputDto;

class UserInputMapper
{
    /**
     * Преобразует массив данных в UserInputDto.
     *
     * @param  array<string, mixed> $data
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
}
