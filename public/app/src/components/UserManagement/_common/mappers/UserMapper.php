<?php

namespace crm\src\components\UserManagement\_common\mappers;

use crm\src\components\UserManagement\_entities\User;
use crm\src\components\UserManagement\_common\adapters\UserResult;
use crm\src\components\UserManagement\_common\interfaces\IUserResult;
use crm\src\components\UserManagement\_exceptions\UserManagementException;

class UserMapper
{
    /**
     * @param  array<string, mixed> $data
     * @return IUserResult
     */
    public static function fromArray(array $data): IUserResult
    {
        if (!isset($data['login'], $data['password_hash'])) {
            return UserResult::failure(
                new UserManagementException('Missing required user fields: login or password_hash')
            );
        }

        $user = new User(
            login: (string) $data['login'],
            passwordHash: (string) $data['password_hash'],
            id: isset($data['id']) ? (int) $data['id'] : null
        );

        return UserResult::success($user);
    }
}
