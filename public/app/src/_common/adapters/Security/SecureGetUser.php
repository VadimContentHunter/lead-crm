<?php

namespace crm\src\_common\adapters\Security;

use crm\src\_common\interfaces\IValidation;
use crm\src\components\Security\SecureWrapper;
use crm\src\components\UserManagement\GetUser;
use crm\src\_common\adapters\UserValidatorAdapter;
use crm\src\components\UserManagement\_entities\User;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\UserManagement\_common\DTOs\UserInputDto;
use crm\src\components\UserManagement\_common\DTOs\UserFilterDto;
use crm\src\components\Security\_common\interfaces\IAccessGranter;
use crm\src\components\UserManagement\_common\interfaces\IGetUser;
use crm\src\components\UserManagement\_common\interfaces\IUserResult;
use crm\src\components\UserManagement\_common\interfaces\IUserRepository;

class SecureGetUser implements IGetUser
{
    private SecureWrapper $secure;

    public function __construct(
        IUserRepository $userRepository,
        IValidation $validator,
        IAccessGranter $accessGranter,
        ?AccessContext $accessContext
    ) {
        // Создаем реальный target
        $target = new GetUser($userRepository, $validator);

        // Оборачиваем его SecureWrapper'ом
        $this->secure = new SecureWrapper($target, $accessGranter, $accessContext);
    }

    public function executeById(int $id): IUserResult
    {
        return $this->secure->executeById($id);
    }

    public function executeByLogin(string $login): IUserResult
    {
        return $this->secure->executeByLogin($login);
    }

    public function executeByDto(UserInputDto $dto): IUserResult
    {
        return $this->secure->executeByDto($dto);
    }

    public function executeAll(): IUserResult
    {
        return $this->secure->executeAll();
    }

    public function executeColumnNames(array $renameMap = []): IUserResult
    {
        return $this->secure->executeColumnNames($renameMap);
    }

    public function executeAllMapped(callable $mapper): IUserResult
    {
        return $this->secure->executeAllMapped($mapper);
    }

    public function filtered(UserFilterDto $filter): IUserResult
    {
        return $this->secure->filtered($filter);
    }
}
