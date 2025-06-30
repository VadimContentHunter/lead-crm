<?php

namespace crm\src\components\UserManagement;

use crm\src\_common\interfaces\IValidation;
use crm\src\components\UserManagement\GetUser;
use crm\src\components\UserManagement\CreateUser;
use crm\src\components\UserManagement\DeleteUser;
use crm\src\components\UserManagement\UpdateUser;
use crm\src\components\UserManagement\_common\interfaces\IUserRepository;

class UserManagement
{
    private ?CreateUser $create = null;
    private ?GetUser $get = null;
    private ?UpdateUser $update = null;
    private ?DeleteUser $delete = null;

    public function __construct(
        private IUserRepository $repository,
        private IValidation $validator,
    ) {
    }

    public function create(): CreateUser
    {
        return $this->create ??= new CreateUser($this->repository, $this->validator);
    }

    public function get(): GetUser
    {
        return $this->get ??= new GetUser($this->repository, $this->validator);
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
