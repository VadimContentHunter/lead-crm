<?php

namespace crm\src\_common\adapters\Security\BasedAccessGranter;

use crm\src\components\Security\RoleNames;
use crm\src\_common\repositories\UserRepository;
use crm\src\components\Security\_common\DTOs\AccessFullContextDTO;
use crm\src\components\Security\_exceptions\JsonRpcSecurityException;
use crm\src\components\LeadManagement\_common\interfaces\ILeadRepository;
use crm\src\components\Security\_common\interfaces\IAccessRoleRepository;
use crm\src\components\UserManagement\_common\interfaces\IUserRepository;
use crm\src\components\Security\_common\interfaces\IAccessSpaceRepository;
use crm\src\_common\adapters\Security\BasedAccessGranter\IRoleAccessHandler;
use crm\src\components\Security\_common\interfaces\IAccessContextRepository;

class AdminRoleHandler implements IRoleAccessHandler
{
    public function __construct(
        private IAccessContextRepository $contextRepository,
        private IAccessRoleRepository $roleRepository,
        private IAccessSpaceRepository $spaceRepository,
        private IUserRepository $userRepository,
        private ILeadRepository $leadRepository
    ) {
    }

    public function supports(object $target, string $methodName, AccessFullContextDTO $context): bool
    {
        return RoleNames::isAnyAdmin($context->role->name ?? '');
    }

    public function handle(AccessFullContextDTO $context, object $target, string $methodName, array $args): mixed
    {
        if ($target instanceof UserRepository && $methodName === 'deleteById') {
            $userId = $args[0];
            $leads = $this->leadRepository->getLeadsByManagerId((int)$userId);
            if (count($leads) > 0) {
                throw new JsonRpcSecurityException("Нельзя удалить пользователя, у которого есть лиды.");
            }

            $user = $this->userRepository->getById($userId);
            $context = $this->contextRepository->getByUserId($userId);
            $role = $this->roleRepository->getById($context?->roleId ?? 0);
            if (RoleNames::isSuperAdmin($role->name ?? '')) {
                throw new JsonRpcSecurityException("Нельзя удалить суперадмина.");
            }

            if (RoleNames::isTeamManager($role->name ?? '')) {
                $groupUsers = $this->contextRepository->getAllBySpaceId($context?->spaceId ?? 0);
                if (count($groupUsers) > 1) {
                    throw new JsonRpcSecurityException("Нельзя удалить менеджера команды, у которого есть пользователи.");
                }
            }

            return $target->$methodName(...$args);
        }

        return $target->$methodName(...$args);
    }
}
