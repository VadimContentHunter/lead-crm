<?php

namespace crm\src\components\UserManagement;

use crm\src\_common\interfaces\IValidation;
use crm\src\components\UserManagement\_entities\User;
use crm\src\components\UserManagement\_common\DTOs\UserInputDto;
use crm\src\components\UserManagement\_common\adapters\UserResult;
use crm\src\components\UserManagement\_common\interfaces\IUserResult;
use crm\src\components\UserManagement\_common\interfaces\IUserRepository;
use crm\src\components\UserManagement\_common\mappers\UserMapper;
use crm\src\components\UserManagement\_exceptions\UserManagementException;
use crm\src\controllers\UserPage;

class UpdateUser
{
    public function __construct(
        private IUserRepository $userRepository,
        private IValidation $validator,
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
        $ignoredFields = [];
        if (empty($dto->plainPassword)) {
            $ignoredFields = ['plainPassword', 'confirmPassword'];
        }

        $validationResult = $this->validator->validate($dto, $ignoredFields);
        if (!$validationResult->isValid()) {
            return UserResult::failure(
                new UserManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        $user = ['id' => $dto->id];

        if (!empty($dto->login)) {
            $user['login'] = $dto->login;
        }

        if (!empty($dto->plainPassword)) {
            $user['password_hash'] = password_hash($dto->plainPassword, PASSWORD_DEFAULT);
        }

        try {
            $idUpdated = $this->userRepository->update($user);
            if ($idUpdated < 0) {
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
