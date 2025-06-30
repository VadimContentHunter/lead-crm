<?php

namespace crm\src\components\UserManagement;

use crm\src\components\UserManagement\common\DTOs\UserDto;
use crm\src\components\UserManagement\common\adapters\UserResult;
use crm\src\components\UserManagement\common\interfaces\IUserResult;
use crm\src\components\UserManagement\common\interfaces\IUserRepository;
use crm\src\components\UserManagement\common\interfaces\IUserValidation;
use crm\src\components\UserManagement\common\exceptions\UserManagementException;

class DeleteUser
{
    public function __construct(
        private IUserRepository $userRepository,
        private IUserValidation $validator,
    ) {
    }

    /**
     * Удаляет пользователя по ID.
     *
     * @param  int $id ID пользователя.
     * @return IUserResult Результат операции: успешный с ID удалённого пользователя или с ошибкой.
     */
    public function executeById(int $id): IUserResult
    {
        try {
            $deletedId = $this->userRepository->deleteById($id);

            if ($deletedId === null) {
                return UserResult::failure(
                    new UserManagementException("Пользователь с ID {$id} не найден или не удалён")
                );
            }

            return UserResult::success($deletedId);
        } catch (\Throwable $e) {
            return UserResult::failure($e);
        }
    }

    /**
     * Удаляет пользователя по логину.
     *
     * @param  string $login Логин пользователя.
     * @return IUserResult Результат операции: успешный с ID удалённого пользователя или с ошибкой.
     */
    public function executeByLogin(string $login): IUserResult
    {
        try {
            $deletedId = $this->userRepository->deleteByLogin($login);

            if ($deletedId === null) {
                return UserResult::failure(
                    new UserManagementException("Пользователь с логином '{$login}' не найден или не удалён")
                );
            }

            return UserResult::success($deletedId);
        } catch (\Throwable $e) {
            return UserResult::failure($e);
        }
    }

    public function executeByDto(UserDto $dto): IUserResult
    {
        $validationResult = $this->validator->validate($dto);

        if (!$validationResult->isValid()) {
            return UserResult::failure(
                new UserManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        try {
            // Предполагаем, что валидатор гарантирует наличие одного из полей
            return $dto->id > 0
            ? $this->executeById($dto->id)
            : $this->executeByLogin($dto->login);
        } catch (\Throwable $e) {
            return UserResult::failure($e);
        }
    }
}
