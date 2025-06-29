<?php

namespace crm\src\components\UserManagement;

use InvalidArgumentException;
use crm\src\components\UserManagement\entities\User;
use crm\src\components\UserManagement\common\DTOs\UserDto;
use crm\src\components\UserManagement\common\interfaces\IUserRepository;
use crm\src\components\UserManagement\common\interfaces\IUserValidation;

class CreateUser
{
    public function __construct(
        private IUserRepository $userRepository,
        private IUserValidation $validator,
    ) {
    }

    /**
     * Создаёт пользователя.
     *
     * @param  UserDto $dto Данные нового пользователя с plain паролем.
     * @return int|null Возвращает ID созданного пользователя или null при ошибке.
     *
     * @throws InvalidArgumentException При ошибках валидации.
     */
    public function execute(UserDto $dto): ?int
    {
        // Валидируем DTO
        $this->validator->validate($dto);

        // Создаём доменную сущность User с хешированным паролем
        $user = new User(
            login: $dto->login,
            passwordHash: password_hash($dto->plainPassword, PASSWORD_DEFAULT),
        );

        // Сохраняем пользователя через репозиторий и возвращаем ID
        return $this->userRepository->save($user);
    }
}
