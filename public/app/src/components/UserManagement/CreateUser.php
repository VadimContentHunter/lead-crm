<?php

namespace crm\src\components\UserManagement;

use InvalidArgumentException;
use crm\src\components\UserManagement\entities\User;
use crm\src\components\UserManagement\common\DTOs\UserDto;
use crm\src\components\UserManagement\common\adapters\UserResult;
use crm\src\components\UserManagement\common\interfaces\IUserResult;
use crm\src\components\UserManagement\common\interfaces\IUserRepository;
use crm\src\components\UserManagement\common\interfaces\IUserValidation;
use crm\src\components\UserManagement\common\exceptions\UserManagementException;

class CreateUser
{
    public function __construct(
        private IUserRepository $userRepository,
        private IUserValidation $validator,
    ) {
    }

    /**
     * Создаёт нового пользователя на основе DTO.
     *
     * Проводит валидацию данных, хеширует пароль, сохраняет пользователя в репозиторий
     * и возвращает результат операции с объектом пользователя или ошибкой.
     *
     * @param  UserDto $dto DTO с данными нового пользователя (содержит plainPassword).
     * @return IUserResult Результат операции: успешный с User или неуспешный с ошибкой.
     *
     * @throws UserManagementException Если валидация не пройдена или пользователь не сохранён.
     * @throws \Throwable В случае неожиданных ошибок при сохранении пользователя.
     */
    public function execute(UserDto $dto): IUserResult
    {
        $validationResult = $this->validator->validate($dto);

        if (!$validationResult->isValid()) {
            return UserResult::failure(
                new UserManagementException(implode('; ', $validationResult->getErrors()))
            );
        }

        $user = new User(
            login: $dto->login,
            passwordHash: password_hash($dto->plainPassword, PASSWORD_DEFAULT),
        );

        try {
            $userId = $this->userRepository->save($user);
            if (!is_int($userId) || $userId <= 0) {
                throw new UserManagementException('Пользователь не сохранён');
            }
            $user->id = $userId;
            return UserResult::success($user);
        } catch (\Throwable $e) {
            return UserResult::failure($e);
        }
    }
}
