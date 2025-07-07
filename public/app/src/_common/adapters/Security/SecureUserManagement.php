<?php

namespace crm\src\_common\adapters\Security;

use crm\src\_common\interfaces\IValidation;
use crm\src\components\UserManagement\GetUser;
use crm\src\components\UserManagement\CreateUser;
use crm\src\components\UserManagement\DeleteUser;
use crm\src\components\UserManagement\UpdateUser;
use crm\src\_common\adapters\Security\SecureGetUser;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_common\interfaces\IAccessGranter;
use crm\src\components\UserManagement\_common\interfaces\IGetUser;
use crm\src\components\UserManagement\_common\interfaces\IUserManagement;
use crm\src\components\UserManagement\_common\interfaces\IUserRepository;

class SecureUserManagement implements IUserManagement
{
    private ?CreateUser $create = null;
    private ?IGetUser $get = null;
    private ?UpdateUser $update = null;
    private ?DeleteUser $delete = null;

    public function __construct(
        private IUserRepository $repository,
        private IValidation $validator,
        private IAccessGranter $accessGranter,
        private ?AccessContext $accessContext
    ) {
    }

    public function create(): CreateUser
    {
        return $this->create ??= new CreateUser($this->repository, $this->validator);
    }

    public function get(): IGetUser
    {
        return $this->get ??= new SecureGetUser($this->repository, $this->validator, $this->accessGranter, $this->accessContext);
    }

    public function update(): UpdateUser
    {
        return $this->update ??= new UpdateUser($this->repository, $this->validator);
    }

    public function delete(): DeleteUser
    {
        return $this->delete ??= new DeleteUser($this->repository, $this->validator);
    }
}
