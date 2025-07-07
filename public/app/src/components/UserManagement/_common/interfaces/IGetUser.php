<?php

namespace crm\src\components\UserManagement\_common\interfaces;

use crm\src\components\UserManagement\_common\DTOs\UserInputDto;
use crm\src\components\UserManagement\_common\DTOs\UserFilterDto;
use crm\src\components\UserManagement\_entities\User;
use crm\src\components\UserManagement\_common\interfaces\IUserResult;

interface IGetUser
{
    /**
     * Получает пользователя по ID.
     *
     * @param  int $id
     * @return IUserResult
     */
    public function executeById(int $id): IUserResult;

    /**
     * Получает пользователя по логину.
     *
     * @param  string $login
     * @return IUserResult
     */
    public function executeByLogin(string $login): IUserResult;

    /**
     * Получает пользователя по DTO, где указан ID или логин.
     *
     * @param  UserInputDto $dto
     * @return IUserResult
     */
    public function executeByDto(UserInputDto $dto): IUserResult;

    /**
     * Получает всех пользователей.
     *
     * @return IUserResult
     */
    public function executeAll(): IUserResult;

    /**
     * Возвращает названия столбцов таблицы пользователей.
     *
     * @param  array<string, string> $renameMap
     * @return IUserResult
     */
    public function executeColumnNames(array $renameMap = []): IUserResult;

    /**
     * Получает всех пользователей с применением маппера к каждому элементу.
     *
     * @template T
     * @param    callable(User): T $mapper
     * @return   IUserResult
     */
    public function executeAllMapped(callable $mapper): IUserResult;

    /**
     * Получает пользователей по фильтру.
     *
     * @param  UserFilterDto $filter
     * @return IUserResult
     */
    public function filtered(UserFilterDto $filter): IUserResult;
}
