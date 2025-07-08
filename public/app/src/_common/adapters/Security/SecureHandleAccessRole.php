<?php

namespace crm\src\_common\adapters\Security;

use crm\src\components\Security\SecureWrapper;
use crm\src\components\Security\_entities\AccessRole;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_handlers\HandleAccessRole;
use crm\src\components\Security\_common\interfaces\IAccessGranter;
use crm\src\components\Security\_common\interfaces\IHandleAccessRole;
use crm\src\components\Security\_common\interfaces\IAccessRoleRepository;

class SecureHandleAccessRole implements IHandleAccessRole
{
    private SecureWrapper $secure;

    public function __construct(
        IAccessRoleRepository $roleRepository,
        IAccessGranter $accessGranter,
        ?AccessContext $accessContext
    ) {
        $target = new HandleAccessRole($roleRepository);
        $this->secure = new SecureWrapper($target, $accessGranter, $accessContext);
    }

    public function addRole(string $name, ?string $description = null): ?AccessRole
    {
        return $this->secure->__call('addRole', [$name, $description]);
    }

    public function editRoleById(int $roleId, ?string $newName = null, ?string $newDescription = null): bool
    {
        return $this->secure->__call('editRoleById', [$roleId, $newName, $newDescription]);
    }

    public function editRoleByName(string $roleName, ?string $newName = null, ?string $newDescription = null): bool
    {
        return $this->secure->__call('editRoleByName', [$roleName, $newName, $newDescription]);
    }

    public function deleteRole(int $roleId): bool
    {
        return $this->secure->__call('deleteRole', [$roleId]);
    }

    public function getRoleById(int $roleId): ?AccessRole
    {
        return $this->secure->__call('getRoleById', [$roleId]);
    }

    public function getRoleByName(string $name): ?AccessRole
    {
        return $this->secure->__call('getRoleByName', [$name]);
    }

    public function getAllRoles(): array
    {
        return $this->secure->__call('getAllRoles', []);
    }

    public function getAllExceptRoles(string $column = '', array $excludedValues = []): array
    {
        return $this->secure->__call('getAllExceptRoles', [$column, $excludedValues]);
    }
}
