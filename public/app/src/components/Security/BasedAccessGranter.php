<?php

namespace crm\src\components\Security;

use crm\src\controllers\LeadPage;
use crm\src\controllers\LoginPage;
use crm\src\controllers\LogoutPage;
use crm\src\components\Security\RoleNames;
use crm\src\controllers\NotFoundController;
use crm\src\controllers\API\LoginController;
use crm\src\controllers\BootstrapController;
use crm\src\components\Security\_entities\AccessRole;
use crm\src\components\Security\_entities\AccessSpace;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_common\interfaces\IAccessGranter;
use crm\src\components\Security\_common\interfaces\IAccessRoleRepository;
use crm\src\components\Security\_common\interfaces\IAccessSpaceRepository;
use crm\src\components\Security\_exceptions\AuthenticationRequiredException;

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
        if ($this->isAllowedMethod($methodName)) {
            return true;
        }

        if ($accessContext->roleId === null) {
            return false;
        }

        $role = $this->roleRepository->getById($accessContext->roleId ?? 0);
        $space = $accessContext->spaceId !== null ? $this->spaceRepository->getById($accessContext->spaceId ?? 0) : null;

        if ($role === null) {
            return false;
        }

        if (RoleNames::isAnyAdmin($role->name)) {
            return true;
        }

        if (RoleNames::isManager($role->name)) {
            return $this->checkManagerPermissions($role, $space, $target, $methodName, $args);
        }

        if (RoleNames::isTeamManager($role->name)) {
            return $this->checkTeamManagerPermissions($role, $space, $target, $methodName, $args);
        }

        return false;
    }

    public function canCreate(string $className, ?AccessContext $accessContext): bool
    {
        if ($this->isAllowedWithoutAuth($className)) {
            return true;
        }

        $role = $this->roleRepository->getById($accessContext?->roleId ?? 0);
        if ($role instanceof AccessRole) {
            return true;
        }

        throw new AuthenticationRequiredException();
    }

    private function isAllowedWithoutAuth(string $className): bool
    {
        return in_array($className, [
            LoginPage::class,
            LogoutPage::class,
            LoginController::class,
            NotFoundController::class,
            BootstrapController::class,
            // LeadPage::class,
        ], true);
    }

    private function isAllowedMethod(string $methodName): bool
    {
        return in_array($methodName, [
            'show404',
        ], true);
    }

    /**
     * @param mixed[] $args
     */
    private function checkManagerPermissions(AccessRole $role, ?AccessSpace $space, object $target, string $methodName, array $args): bool
    {
        return true;
    }

    /**
     * @param mixed[] $args
     */
    private function checkTeamManagerPermissions(AccessRole $role, ?AccessSpace $space, object $target, string $methodName, array $args): bool
    {
        return true;
    }
}
