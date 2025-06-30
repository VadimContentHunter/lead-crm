<?php

namespace crm\src\components\UserManagement\common\interfaces;

use crm\src\components\UserManagement\entities\User;

interface IUserRepository
{
    /**
     * @return int|null Возвращает id сохраненного пользователя
     */
    public function save(User $user): ?int;

    /**
     * @return int|null Возвращает id удаленного пользователя
     */
    public function deleteByLogin(string $login): ?int;

    /**
     * @return int|null Возвращает id удаленного пользователя
     */
    public function deleteById(int $id): ?int;

    /**
     * @return int|null Возвращает id обновленного пользователя
     */
    public function update(User $user): ?int;
    public function getByLogin(string $login): ?User;

    /**
     * @return User[]
     */
    public function getAll(): array;
    public function getById(int $id): ?User;
}
