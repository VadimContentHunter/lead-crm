<?php

namespace crm\src\components\UserManagement;

use crm\src\components\UserManagement\common\DTOs\UserInputDto;
use crm\src\components\UserManagement\common\interfaces\IUserRepository;
use crm\src\components\UserManagement\common\interfaces\IUserValidation;
use crm\src\components\UserManagement\common\interfaces\IUserResult;
use crm\src\components\UserManagement\common\adapters\UserResult;
use crm\src\components\UserManagement\common\exceptions\UserManagementException;
use crm\src\components\UserManagement\entities\User;

class UpdateUser
{
    public function __construct(
        private IUserRepository $userRepository,
        private IUserValidation $validator,
    ) {
    }

    /**
     * Обновляет пользователя на основе DTO.
     *
     * @param  UserInputDto $dto DTO с обновлёнными данными.
     * @return IUserResult Результат операции: успешный с User или с ошибкой.
     */
    public function execute(UserInputDto $dto): IUserResult
    {
        $validationResult = $this->validator->validate($dto);

        if (!$validationResult->isValid()) {
            return UserResult::failure(
                new UserManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        if (empty($dto->plainPassword)) {
            return UserResult::failure(
                new UserManagementException('Пароль обязателен при обновлении')
            );
        }

        $user = new User(
            login: $dto->login,
            passwordHash: password_hash($dto->plainPassword, PASSWORD_DEFAULT),
            id: $dto->id
        );

        try {
            $updatedId = $this->userRepository->update($user);

            if ($updatedId === null || $updatedId <= 0) {
                return UserResult::failure(
                    new UserManagementException('Не удалось обновить пользователя')
                );
            }

            return UserResult::success($user);
        } catch (\Throwable $e) {
            return UserResult::failure($e);
        }
    }
}
