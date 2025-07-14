<?php

namespace crm\src\components\Security\_common\interfaces;

use crm\src\_common\interfaces\IRepository;
use crm\src\components\Security\_entities\AccessRole;

interface IHandleAccessRole
{
    /**
     * Добавление новой роли.
     */
    public function addRole(string $name, ?string $description = null): ?AccessRole;

    /**
     * Редактирование роли по ID.
     */
    public function editRoleById(int $roleId, ?string $newName = null, ?string $newDescription = null): bool;

    /**
     * Редактирование роли по имени.
     */
    public function editRoleByName(string $roleName, ?string $newName = null, ?string $newDescription = null): bool;

    /**
     * Удаление роли по ID.
     */
    public function deleteRole(int $roleId): bool;

    /**
     * Получение роли по ID.
     */
    public function getRoleById(int $roleId): ?AccessRole;

    /**
     * Получение роли по имени.
     */
    public function getRoleByName(string $name): ?AccessRole;

    /**
     * @return AccessRole[]
     */
    public function getAllRoles(): array;

    /**
     * @param  array<int|string> $excludedValues
     * @return AccessRole[]
     */
    public function getAllExceptRoles(string $column = '', array $excludedValues = []): array;
}
