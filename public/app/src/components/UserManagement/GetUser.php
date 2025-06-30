<?php

namespace crm\src\components\UserManagement;

use crm\src\_common\interfaces\IValidation;
use crm\src\components\UserManagement\_common\DTOs\UserInputDto;
use crm\src\components\UserManagement\_common\adapters\UserResult;
use crm\src\components\UserManagement\_common\interfaces\IUserResult;
use crm\src\components\UserManagement\_common\interfaces\IUserRepository;
use crm\src\components\UserManagement\_exceptions\UserManagementException;

class GetUser
{
    public function __construct(
        private IUserRepository $userRepository,
        private IValidation $validator,
    ) {
    }

    /**
     * Получает пользователя по ID.
     *
     * @param  int $id
     * @return IUserResult
     */
    public function executeById(int $id): IUserResult
    {
        try {
            $user = $this->userRepository->getById($id);

            if ($user === null) {
                return UserResult::failure(
                    new UserManagementException("Пользователь с ID {$id} не найден")
                );
            }

            return UserResult::success($user);
        } catch (\Throwable $e) {
            return UserResult::failure($e);
        }
    }

    /**
     * Получает пользователя по логину.
     *
     * @param  string $login
     * @return IUserResult
     */
    public function executeByLogin(string $login): IUserResult
    {
        try {
            $user = $this->userRepository->getByLogin($login);

            if ($user === null) {
                return UserResult::failure(
                    new UserManagementException("Пользователь с логином '{$login}' не найден")
                );
            }

            return UserResult::success($user);
        } catch (\Throwable $e) {
            return UserResult::failure($e);
        }
    }

    /**
     * Получает пользователя по DTO, где указан ID или логин.
     *
     * @param  UserInputDto $dto
     * @return IUserResult
     */
    public function executeByDto(UserInputDto $dto): IUserResult
    {
        $validationResult = $this->validator->validate($dto);

        if (!$validationResult->isValid()) {
            return UserResult::failure(
                new UserManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        return $dto->id > 0
            ? $this->executeById($dto->id)
            : $this->executeByLogin($dto->login);
    }
}
