<?php

namespace crm\src\_common\adapters\Security;

use crm\src\controllers\LeadPage;
use crm\src\controllers\UserPage;
use crm\src\controllers\LoginPage;
use crm\src\controllers\LogoutPage;
use crm\src\components\Security\RoleNames;
use crm\src\controllers\API\UserController;
use crm\src\controllers\NotFoundController;
use crm\src\controllers\API\LoginController;
use crm\src\controllers\BootstrapController;
use crm\src\services\AppContext\IAppContext;
use crm\src\components\UserManagement\GetUser;
use crm\src\components\Security\_entities\AccessRole;
use crm\src\components\UserManagement\_entities\User;
use crm\src\components\Security\_entities\AccessSpace;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_handlers\HandleAccessRole;
use crm\src\components\Security\_handlers\HandleAccessSpace;
use crm\src\components\Security\_exceptions\SecurityException;
use crm\src\components\UserManagement\_common\DTOs\UserFilterDto;
use crm\src\components\UserManagement\_common\mappers\UserMapper;
use crm\src\components\Security\_common\DTOs\AccessFullContextDTO;
use crm\src\components\Security\_common\interfaces\IAccessGranter;
use crm\src\components\Security\_exceptions\JsonRpcSecurityException;
use crm\src\components\Security\_common\mappers\AccessFullContextMapper;
use crm\src\components\Security\_common\interfaces\IAccessRoleRepository;
use crm\src\components\Security\_common\interfaces\IAccessSpaceRepository;
use crm\src\components\Security\_exceptions\AuthenticationRequiredException;

class BasedAccessGranter implements IAccessGranter
{
    public function __construct(
        private IAccessRoleRepository $roleRepository,
        private IAccessSpaceRepository $spaceRepository
    ) {
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

    /**
     * Выполняет вызов метода, выбирая поведение по ролям и пространству.
     *
     * @param  object        $target
     * @param  string        $methodName
     * @param  mixed[]       $args
     * @param  AccessContext $accessContext
     * @return mixed
     */
    public function callWithAccessCheck(
        object $target,
        string $methodName,
        array $args,
        AccessContext $accessContext
    ): mixed {
        if (!method_exists($target, $methodName)) {
            throw new SecurityException("Метод $methodName не существует в целевом объекте.");
        }

        if ($this->isAllowedMethod($methodName)) {
            // Просто вызываем оригинальный метод
            return $target->$methodName(...$args);
        }

        if ($accessContext->roleId === null) {
            throw new SecurityException("Пользователь не авторизован");
        }

        $role = $this->roleRepository->getById($accessContext->roleId);
        $space = $accessContext->spaceId !== null
            ? $this->spaceRepository->getById($accessContext->spaceId)
            : null;

        if ($role === null) {
            throw new SecurityException("Роль не найдена");
        }

        // Полное право — вызываем как есть
        if (RoleNames::isAnyAdmin($role->name)) {
            return $target->$methodName(...$args);
        }

        // Менеджер — вызываем особую логику
        if (RoleNames::isManager($role->name)) {
            return $this->handleManagerCall(
                AccessFullContextMapper::fromEntities($accessContext, $role, $space),
                $target,
                $methodName,
                $args
            );
        }

        // Тим-лид — другая логика
        if (RoleNames::isTeamManager($role->name)) {
            return $this->handleTeamManagerCall(
                AccessFullContextMapper::fromEntities($accessContext, $role, $space),
                $target,
                $methodName,
                $args
            );
        }

        throw new SecurityException("Нет доступа для роли {$role->name}");
    }




    private function isAllowedWithoutAuth(string $className): bool
    {
        return in_array($className, [
            LoginPage::class,
            LogoutPage::class,
            LoginController::class,
            NotFoundController::class,
            BootstrapController::class,
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
    private function handleManagerCall(AccessFullContextDTO $accessFullContext, object $target, string $methodName, array $args): mixed
    {
        // throw new SecurityException("Нет доступа для роли {$role->name}");
        if ($target instanceof HandleAccessSpace) {
            switch ($methodName) {
                case 'getAllSpaces':
                    return $target->getAllSpaces('id', [$accessFullContext->getSpaceId() ?? 0]);
                // default:
                //     throw new SecurityException("Метод {$methodName} не разрешён для HandleAccessSpace");
            }
        }

        if ($target instanceof HandleAccessRole) {
            switch ($methodName) {
                case 'getAllExceptRoles':
                    return $this->roleRepository->getAllByColumnValues('name', [ RoleNames::MANAGER->value]);
            }
        }

        if ($target instanceof GetUser) {
            switch ($methodName) {
                case 'executeAllMapped':
                    $a = $target->executeById($accessFullContext->userId)->mapToNew(fn (mixed $data) => [UserMapper::toArray($data)]);
                    return $a;
            }
        }

        if ($target instanceof UserController) {
            // throw new SecurityException("Метод {$methodName} не разрешён для HandleAccessSpace");
            switch ($methodName) {
                case 'deleteUser':
                    throw new JsonRpcSecurityException("Менеджер не может удалить пользователей");
                case 'deleteUserById':
                    throw new JsonRpcSecurityException("Менеджер не может удалить пользователей");
                case 'editUser':
                    throw new JsonRpcSecurityException("Менеджер не может редактировать пользователей");
            }
        }

        if ($target instanceof UserPage) {
            switch ($methodName) {
                case 'showEditUserPage':
                    throw new SecurityException("Менеджер не может редактировать пользователей.");
            }
        }

        return $target->$methodName(...$args);
    }

    /**
     * @param mixed[] $args
     */
    private function handleTeamManagerCall(AccessFullContextDTO $accessFullContext, object $target, string $methodName, array $args): mixed
    {
        return $target->$methodName(...$args);
    }
}
