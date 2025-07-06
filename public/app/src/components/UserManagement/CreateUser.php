<?php

namespace crm\src\components\UserManagement;

use crm\src\_common\interfaces\IValidation;
use crm\src\components\UserManagement\_entities\User;
use crm\src\components\UserManagement\_common\DTOs\UserInputDto;
use crm\src\components\UserManagement\_common\adapters\UserResult;
use crm\src\components\UserManagement\_common\interfaces\IUserResult;
use crm\src\components\UserManagement\_common\interfaces\IUserRepository;
use crm\src\components\UserManagement\_exceptions\UserManagementException;

class CreateUser
{
    public function __construct(
        private IUserRepository $userRepository,
        private IValidation $validator,
    ) {
    }

    /**
     * Создаёт нового пользователя на основе DTO.
     *
     * Проводит валидацию данных, хеширует пароль, сохраняет пользователя в репозиторий
     * и возвращает результат операции с объектом пользователя или ошибкой.
     *
     * @param  UserInputDto $dto DTO с данными нового пользователя (содержит plainPassword).
     * @return IUserResult Результат операции: успешный с User или неуспешный с ошибкой.
     *
     * @throws UserManagementException Если валидация не пройдена или пользователь не сохранён.
     * @throws \Throwable В случае неожиданных ошибок при сохранении пользователя.
     */
    public function execute(UserInputDto $dto): IUserResult
    {
        $validationResult = $this->validator->validate($dto);

        if (!$validationResult->isValid()) {
            return UserResult::failure(
                new UserManagementException(implode('; ', $validationResult->getErrors()))
            );
        }

        $user = new User(
            login: $dto->login,
            passwordHash: $this->generatePasswordHash($dto->plainPassword),
        );

        try {
            $userId = $this->userRepository->save($user);
            if (!is_int($userId) || $userId <= 0) {
                throw new UserManagementException('Пользователь не сохранён');
            }
            $user->id = $userId;
            return UserResult::success($user);
        } catch (\Throwable $e) {
            return UserResult::failure(
                new UserManagementException("Произошла внутренняя ошибка")
            );
        }
    }

    /**
     * Генерация хэша пароля
     *
     * @param  string $plainPassword
     * @return string
     */
    public function generatePasswordHash(string $plainPassword): string
    {
        return password_hash($plainPassword, PASSWORD_DEFAULT);
    }

    /**
     * Проверка соответствия пароля и хэша
     *
     * @param  string $plainPassword
     * @param  string $passwordHash
     * @return bool
     */
    public function validatePassword(string $plainPassword, string $passwordHash): bool
    {
        return password_verify($plainPassword, $passwordHash);
    }
}
