<?php

namespace crm\src\components\Security;

use crm\src\components\Security\_entities\AccessRole;
use crm\src\components\Security\_entities\AccessSpace;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_common\interfaces\IAccessGranter;
use crm\src\components\Security\_common\interfaces\IAccessRoleRepository;
use crm\src\components\Security\_common\interfaces\IAccessSpaceRepository;

class BasedAccessGranter implements IAccessGranter
{
    public function __construct(
        private IAccessRoleRepository $roleRepository,
        private IAccessSpaceRepository $spaceRepository,
    ) {
    }

    /**
     * @param mixed[] $args
     */
    public function canCall(object $target, string $methodName, array $args, AccessContext $accessContext): bool
    {
        if ($accessContext->roleId === null) {
            return false;
        }

        $role = $this->roleRepository->getById($accessContext->roleId);
        $space = $accessContext->spaceId !== null ? $this->spaceRepository->getById($accessContext->spaceId) : null;

        if ($role === null) {
            return false;
        }

        if (strtolower($role->name) === 'admin') {
            return true;
        }

        if (strtolower($role->name) === 'manager') {
            return $this->checkManagerPermissions($role, $space, $target, $methodName, $args);
        }

        if (strtolower($role->name) === 'team-manager') {
            return $this->checkTeamManagerPermissions($role, $space, $target, $methodName, $args);
        }

        return false;
    }

    /**
     * @param mixed[] $args
     */
    private function checkManagerPermissions(AccessRole $role, ?AccessSpace $space, object $target, string $methodName, array $args): bool
    {
        return false;
    }

    /**
     * @param mixed[] $args
     */
    private function checkTeamManagerPermissions(AccessRole $role, ?AccessSpace $space, object $target, string $methodName, array $args): bool
    {
        return false;
    }
}
