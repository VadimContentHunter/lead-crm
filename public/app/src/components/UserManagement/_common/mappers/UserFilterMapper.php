<?php

namespace crm\src\components\UserManagement\_common\mappers;

use crm\src\components\UserManagement\_common\DTOs\UserFilterDto;

class UserFilterMapper
{
    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): UserFilterDto
    {
        return new UserFilterDto(
            search: $data['search'] ?? null,
            login: $data['login'] ?? null,
            sort: $data['sort'] ?? null,
            sortDir: $data['sortDir'] ?? $data['dir'] ?? null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function toArray(UserFilterDto $dto): array
    {
        return [
            'search' => $dto->search,
            'login' => $dto->login,
            'sort' => $dto->sort,
            'sortDir' => $dto->sortDir,
        ];
    }
}
