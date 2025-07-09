<?php

namespace crm\src\_common\adapters\Security\BasedAccessGranter;

use crm\src\controllers\UserPage;
use crm\src\controllers\SourcePage;
use crm\src\controllers\StatusPage;
use crm\src\components\Security\RoleNames;
use crm\src\controllers\API\LeadController;
use crm\src\controllers\API\UserController;
use crm\src\controllers\API\SourceController;
use crm\src\controllers\API\StatusController;
use crm\src\components\UserManagement\GetUser;
use crm\src\components\Security\_handlers\HandleAccessRole;
use crm\src\components\Security\_handlers\HandleAccessSpace;
use crm\src\components\Security\_exceptions\SecurityException;
use crm\src\components\UserManagement\_common\mappers\UserMapper;
use crm\src\components\Security\_common\DTOs\AccessFullContextDTO;
use crm\src\components\Security\_exceptions\JsonRpcSecurityException;
use crm\src\components\Security\_common\interfaces\IAccessRoleRepository;
use crm\src\components\UserManagement\_common\interfaces\IUserRepository;
use crm\src\components\Security\_common\interfaces\IAccessSpaceRepository;
use crm\src\_common\adapters\Security\BasedAccessGranter\IRoleAccessHandler;

class ManagerRoleHandler implements IRoleAccessHandler
{
    public function __construct(
        private IAccessRoleRepository $roleRepository,
        private IAccessSpaceRepository $spaceRepository,
        private IUserRepository $userRepository
    ) {
    }

    public function supports(object $target, string $methodName): bool
    {
        return true; // полная проверка будет внутри handle
    }

    public function handle(AccessFullContextDTO $context, object $target, string $methodName, array $args): mixed
    {
        if ($target instanceof HandleAccessSpace && $methodName === 'getAllSpaces') {
            return $target->getAllSpaces('id', [$context->getSpaceId() ?? 0]);
        }

        if ($target instanceof HandleAccessRole && $methodName === 'getAllExceptRoles') {
            return $this->roleRepository->getAllByColumnValues('name', [RoleNames::MANAGER->value]);
        }

        if ($target instanceof GetUser && $methodName === 'executeAllMapped') {
            return $target->executeById($context->userId)
                ->mapToNew(fn(mixed $data) => [UserMapper::toArray($data)]);
        }

        if ($target instanceof UserController) {
            return $this->handleUserController($context, $target, $methodName, $args);
        }

        if ($target instanceof UserPage && $methodName === 'showEditUserPage') {
            throw new SecurityException("Менеджер не может редактировать пользователей.");
        }

        if ($target instanceof StatusController || $target instanceof SourceController) {
            $entity = $target instanceof StatusController ? 'статусы' : 'источники';
            $this->denyIfIn($methodName, ['createStatus', 'deleteStatus', 'createSource', 'deleteSource'], "Менеджер не может {$this->actionLabel($methodName)} {$entity}.");
        }

        if ($target instanceof StatusPage && $methodName === 'showAddStatusPage') {
            throw new SecurityException("Менеджер не может посетить страницу создания статуса.");
        }

        if ($target instanceof SourcePage && $methodName === 'showAddSourcePage') {
            throw new SecurityException("Менеджер не может посетить страницу создания источника.");
        }

        if ($target instanceof LeadController && $methodName === 'createLead') {
            $leadAccountManagerId = $args[0]['accountManagerId'] ?? null;
            if (filter_var($leadAccountManagerId, FILTER_VALIDATE_INT) === false) {
                return $target->$methodName(...$args);
            }

            $leadSpace = $this->spaceRepository->getById($leadAccountManagerId ?? 0);
            $thisSpace = $this->spaceRepository->getById($context->getSpaceId() ?? 0);
            if ($leadSpace?->id !== $thisSpace?->id) {
                throw new JsonRpcSecurityException("Менеджер может создавать лиды только в пространстве своего менеджера или в своем.");
            }
        }

        return $target->$methodName(...$args);
    }

    private function handleUserController(AccessFullContextDTO $context, UserController $target, string $method, array $args): mixed
    {
        if (in_array($method, ['deleteUser', 'deleteUserById', 'editUser'], true)) {
            throw new JsonRpcSecurityException("Менеджер не может {$this->actionLabel($method)} пользователей.");
        }

        if (in_array($method, ['filterUsers', 'filterUsersFormatTable'], true)) {
            $userLogin = $this->userRepository->getById($context->userId)?->login;
            $argSearch = $args[0]['search'] ?? '';

            $login = $userLogin ?? '--';
            $search = ($argSearch !== $login && (int)$argSearch !== $context->userId) ? '--' : $argSearch;
            $search = $argSearch === '' ? $login : $search;

            return $target->filterUsersFormatTable(['login' => $login, 'search' => $search]);
        }

        if ($method === 'createUser') {
            $role_id = $args[0]['role_id'] ?? null;
            $space_id = $args[0]['space_id'] ?? null;

            $role = $this->roleRepository->getById($role_id);
            if ($role === null) {
                 return $target->createUser($args[0]);
            }

            if ($space_id !== null) {
                $space = $this->spaceRepository->getById($space_id);
            }

            // 3. Проверка ограничений для выбранной роли
            if (!RoleNames::isManager($role->name)) {
                throw new JsonRpcSecurityException("Данная роль не предназначена для создания пользователей.");
            }

            // 4. Проверка пространства для роли менеджера
            if ($context->getSpaceId() !== $space?->id) {
                throw new JsonRpcSecurityException("Менеджер может добавлять только в свое пространство.");
            }

            $args[0]['space_id'] = $space?->id;
            $args[0]['role_id'] = $role->id;

            return $target->createUser($args[0]);
        }

        return $target->$method(...$args);
    }

    private function denyIfIn(string $method, array $restricted, string $message): void
    {
        if (in_array($method, $restricted, true)) {
            throw new JsonRpcSecurityException($message);
        }
    }

    private function actionLabel(string $methodName): string
    {
        return str_starts_with($methodName, 'delete') ? 'удалять' :
            (str_starts_with($methodName, 'create') ? 'создавать' :
            (str_starts_with($methodName, 'edit') ? 'редактировать' : 'использовать'));
    }
}
