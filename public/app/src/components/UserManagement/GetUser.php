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
     * Возвращает всех пользователей в виде таблицы (массив строк).
     *
     * @param  bool $includeHeader Включать ли заголовок в первую строку
     * @param  array<string, string> $renameMap     Переименование колонок:
     *                                              ['db_column' => 'Заголовок']
     * @return IUserResult
     */
    public function executeAsTable(bool $includeHeader = true, array $renameMap = []): IUserResult
    {
        try {
            $users = $this->userRepository->getAll(); // array<User>
            $columns = $this->userRepository->getColumnNames();

            // Заголовок
            $header = array_map(
                fn($col) => $renameMap[$col] ?? $col,
                $columns
            );

            // Преобразуем объекты пользователей в строки
            $rows = [];
            foreach ($users as $user) {
                $row = [];
                foreach ($columns as $col) {
                    $row[] = $user->{$col} ?? null;
                }
                $rows[] = $row;
            }

            if ($includeHeader) {
                array_unshift($rows, $header);
            }

            return UserResult::success($rows);
        } catch (\Throwable $e) {
            return UserResult::failure($e);
        }
    }
}
