<?php

namespace crm\src\_common\adapters\Security\BasedAccessGranter;

use crm\src\controllers\LoginPage;
use crm\src\controllers\LogoutPage;
use crm\src\components\Security\RoleNames;
use crm\src\controllers\NotFoundController;
use crm\src\controllers\API\LoginController;
use crm\src\controllers\BootstrapController;
use crm\src\components\Security\_entities\AccessRole;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_exceptions\SecurityException;
use crm\src\components\Security\_common\DTOs\AccessFullContextDTO;
use crm\src\components\Security\_common\interfaces\IAccessGranter;
use crm\src\components\Security\_common\mappers\AccessFullContextMapper;
use crm\src\components\Security\_common\interfaces\IAccessRoleRepository;
use crm\src\components\UserManagement\_common\interfaces\IUserRepository;
use crm\src\components\Security\_common\interfaces\IAccessSpaceRepository;
use crm\src\_common\adapters\Security\BasedAccessGranter\ManagerRoleHandler;
use crm\src\components\Security\_exceptions\AuthenticationRequiredException;
use crm\src\_common\adapters\Security\BasedAccessGranter\TeamManagerRoleHandler;
use crm\src\components\Security\_common\interfaces\IAccessContextRepository;

class BasedAccessGranter implements IAccessGranter
{
    /**
     * @var IRoleAccessHandler[]
     */
    private array $roleHandlers;

    public function __construct(
        private IAccessContextRepository $contextRepository,
        private IAccessRoleRepository $roleRepository,
        private IAccessSpaceRepository $spaceRepository,
        private IUserRepository $userRepository,
    ) {
        $this->roleHandlers = [
            new ManagerRoleHandler(
                // $this->contextRepository,
                $this->roleRepository,
                $this->spaceRepository,
                $this->userRepository
            ),
            new TeamManagerRoleHandler(
                $this->contextRepository,
                $this->roleRepository,
                $this->spaceRepository,
                $this->userRepository
            ),
        // в будущем другие обработчики ролей
        ];
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

    public function callWithAccessCheck(
        object $target,
        string $methodName,
        array $args,
        ?AccessContext $accessContext
    ): mixed {
        if (!method_exists($target, $methodName)) {
            throw new SecurityException("Метод $methodName не существует в целевом объекте.");
        }

        if ($this->isAllowedMethod($methodName)) {
            return $target->$methodName(...$args);
        }

        if ($accessContext === null || $accessContext->roleId === null) {
            throw new AuthenticationRequiredException("Пользователь не авторизован");
        }

        $role = $this->roleRepository->getById($accessContext->roleId);
        $space = $accessContext->spaceId !== null
            ? $this->spaceRepository->getById($accessContext->spaceId)
            : null;

        if ($role === null) {
            throw new AuthenticationRequiredException("Роль не найдена");
        }

        if (RoleNames::isAnyAdmin($role->name)) {
            return $target->$methodName(...$args);
        }

        $fullContext = AccessFullContextMapper::fromEntities($accessContext, $role, $space);

        foreach ($this->roleHandlers as $handler) {
            if ($handler->supports($target, $methodName, $fullContext)) {
                return $handler->handle($fullContext, $target, $methodName, $args);
            }
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
            'executeByLogin',
        ], true);
    }

    /**
     * @param mixed[] $args
     */
    private function handleTeamManagerCall(AccessFullContextDTO $accessFullContext, object $target, string $methodName, array $args): mixed
    {
        return $target->$methodName(...$args);
    }
}
