<?php

namespace crm\src\_common\repositories;

use crm\src\components\UserManagement\_entities\User;
use crm\src\components\UserManagement\_common\interfaces\IUserRepository;

class UserRepository implements IUserRepository
{
    public function __construct()
    {
    }

    public function deleteByLogin(string $login): ?int
    {
        // TODO: implement deletion by login
        return null;
    }

    public function getByLogin(string $login): ?User
    {
        // TODO: implement getting by login
        return null;
    }

    public function save(object $entity): ?int
    {
        // TODO: implement saving a user
        return null;
    }

    public function update(object $entity): ?int
    {
        // TODO: implement updating a user
        return null;
    }

    public function deleteById(int $id): ?int
    {
        // TODO: implement deletion by id
        return null;
    }

    public function getById(int $id): ?User
    {
        // TODO: implement getting by id
        return null;
    }

    public function getAll(): array
    {
        // TODO: implement getting all users
        return [];
    }
}
