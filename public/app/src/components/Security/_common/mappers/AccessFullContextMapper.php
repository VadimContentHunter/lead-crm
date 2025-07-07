<?php

namespace crm\src\components\Security\_common\mappers;

use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_entities\AccessRole;
use crm\src\components\Security\_entities\AccessSpace;
use crm\src\components\Security\_common\DTOs\AccessFullContextDTO;

class AccessFullContextMapper
{
    /**
     * Создаёт AccessFullContextDTO из сущностей.
     */
    public static function fromEntities(
        AccessContext $context,
        ?AccessRole $role,
        ?AccessSpace $space
    ): AccessFullContextDTO {
        return new AccessFullContextDTO(
            userId: $context->userId,
            sessionAccessHash: $context->sessionAccessHash,
            contextId: $context->id,
            role: $role,
            space: $space
        );
    }

    /**
     * Преобразует DTO обратно в AccessContext.
     */
    public static function toAccessContext(AccessFullContextDTO $dto): AccessContext
    {
        return new AccessContext(
            userId: $dto->userId,
            sessionAccessHash: $dto->sessionAccessHash,
            id: $dto->contextId,
            roleId: $dto->role?->id,
            spaceId: $dto->space?->id
        );
    }

    /**
     * Преобразует DTO в массив для вывода (например, API или логов).
     *
     * @return array<string, mixed>
     */
    public static function toArray(AccessFullContextDTO $dto): array
    {
        return [
            'userId' => $dto->userId,
            'sessionAccessHash' => $dto->sessionAccessHash,
            'contextId' => $dto->contextId,
            'role' => $dto->role ? [
                'id' => $dto->role->id,
                'name' => $dto->role->name,
                'description' => $dto->role->description,
            ] : null,
            'space' => $dto->space ? [
                'id' => $dto->space->id,
                'name' => $dto->space->name,
                'description' => $dto->space->description,
            ] : null,
        ];
    }

    /**
     * Преобразует DTO в массив, исключая пустые значения.
     *
     * @return array<string, mixed>
     */
    public static function toNonEmptyArray(AccessFullContextDTO $dto): array
    {
        return array_filter(
            self::toArray($dto),
            fn($value) => $value !== null && $value !== '',
            ARRAY_FILTER_USE_BOTH
        );
    }
}
