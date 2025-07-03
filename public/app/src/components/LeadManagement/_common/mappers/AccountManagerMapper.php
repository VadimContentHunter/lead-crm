<?php

namespace crm\src\components\LeadManagement\_common\mappers;

use crm\src\components\LeadManagement\_common\DTOs\AccountManagerDto;
use crm\src\components\UserManagement\_entities\User;

class AccountManagerMapper
{
    /**
     * Преобразует массив данных или объект User в AccountManagerDto.
     *
     * @param  array<string, mixed>|User $data
     * @return AccountManagerDto|null
     */
    public static function fromData(array|User $data): ?AccountManagerDto
    {
        if ($data instanceof User) {
            return new AccountManagerDto(
                id: $data->id,
                login: $data->login,
            );
        }

        if (!isset($data['login'])) {
            return null;
        }

        return new AccountManagerDto(
            id: isset($data['id']) ? (int)$data['id'] : null,
            login: (string)$data['login'],
        );
    }

    /**
     * Преобразует AccountManagerDto в массив для сохранения.
     *
     * @param  AccountManagerDto $dto
     * @return array<string, mixed>
     */
    public static function toArray(AccountManagerDto $dto): array
    {
        return [
            'id' => $dto->id,
            'login' => $dto->login,
        ];
    }
}
