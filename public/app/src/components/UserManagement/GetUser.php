<?php

namespace crm\src\components\UserManagement;

use crm\src\_common\interfaces\IValidation;
use crm\src\components\UserManagement\_entities\User;
use crm\src\components\UserManagement\_common\DTOs\UserInputDto;
use crm\src\components\UserManagement\_common\DTOs\UserFilterDto;
use crm\src\components\UserManagement\_common\adapters\UserResult;
use crm\src\components\UserManagement\_common\interfaces\IGetUser;
use crm\src\components\UserManagement\_common\interfaces\IUserResult;
use crm\src\components\UserManagement\_common\interfaces\IUserRepository;
use crm\src\components\UserManagement\_exceptions\UserManagementException;

class GetUser implements IGetUser
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

    /**
     * Получает всех пользователей.
     *
     * @return IUserResult
     */
    public function executeAll(): IUserResult
    {
        try {
            $users = $this->userRepository->getAll();

            return UserResult::success($users);
        } catch (\Throwable $e) {
            return UserResult::failure($e);
        }
    }

    /**
     * Возвращает названия столбцов таблицы пользователей.
     *
     * @param  array<string, string> $renameMap Ключ — оригинальное имя, значение — новое имя
     * @return IUserResult
     */
    public function executeColumnNames(array $renameMap = []): IUserResult
    {
        try {
            $columns = $this->userRepository->getColumnNames();

            if (!empty($renameMap)) {
                $columns = array_map(
                    fn($name) => $renameMap[$name] ?? $name,
                    $columns
                );
            }

            return UserResult::success($columns);
        } catch (\Throwable $e) {
            return UserResult::failure($e);
        }
    }

    /**
     * Получает всех пользователей с применением маппера к каждому элементу.
     *
     * @template T
     * @param    callable(User): T $mapper
     * @return   IUserResult
     */
    public function executeAllMapped(callable $mapper): IUserResult
    {
        try {
            $users = $this->userRepository->getAll();
            $mapped = array_map($mapper, $users);

            return UserResult::success($mapped);
        } catch (\Throwable $e) {
            return UserResult::failure($e);
        }
    }

    public function filtered(UserFilterDto $filter): IUserResult
    {
        try {
            $leads = $this->userRepository->getFilteredUsers($filter);
            if (empty($leads)) {
                return UserResult::failure(new UserManagementException("Пользователи по фильтру не найдены"));
            }
            return UserResult::success($leads);
        } catch (\Throwable $e) {
            return UserResult::failure($e);
        }
    }
}
