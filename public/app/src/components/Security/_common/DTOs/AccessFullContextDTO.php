<?php

namespace crm\src\components\Security\_common\DTOs;

use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_entities\AccessRole;
use crm\src\components\Security\_entities\AccessSpace;

/**
 * DTO, объединяющий AccessContext, AccessRole и AccessSpace в единое представление.
 */
class AccessFullContextDTO
{
    public function __construct(
        public int $userId,
        public ?string $sessionAccessHash = null,
        public ?int $contextId = null,
        public ?AccessRole $role = null,
        public ?AccessSpace $space = null,
    ) {
        if ($this->userId === 0) {
            throw new \InvalidArgumentException('FullAccessContextDto: userId cannot be 0');
        }
    }

    /**
     * Получить имя роли или '---', если роли нет.
     */
    public function getRoleName(): string
    {
        return $this->role?->name ?? '---';
    }

    /**
     * Получить имя пространства или '---', если пространства нет.
     */
    public function getSpaceName(): string
    {
        return $this->space?->name ?? '---';
    }

    /**
     * Получить ID роли.
     */
    public function getRoleId(): ?int
    {
        return $this->role?->id;
    }

    /**
     * Получить ID пространства.
     */
    public function getSpaceId(): ?int
    {
        return $this->space?->id;
    }
}
