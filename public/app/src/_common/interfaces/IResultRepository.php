<?php

namespace crm\src\_common\interfaces;

use crm\src\_common\interfaces\IResult;

/**
 * Расширенный универсальный интерфейс репозитория с результатом-обёрткой.
 *
 * @template TEntity of object
 */
interface IResultRepository
{
    /**
     * Сохраняет сущность и возвращает ID или ошибку.
     *
     * @param  TEntity|array<string, mixed> $entity
     * @return IResult
     */
    public function save(object|array $entity): IResult;

    /**
     * Обновляет сущность или часть полей по id.
     *
     * @param  object|array<string, mixed> $entityOrData
     * @return IResult
     */
    public function update(object|array $entityOrData): IResult;

    /**
     * Удаляет по ID.
     *
     * @param  int $id
     * @return IResult
     */
    public function deleteById(int $id): IResult;

    /**
     * Получает сущность по ID.
     *
     * @param  int $id
     * @return IResult
     */
    public function getById(int $id): IResult;

    /**
     * Получает все сущности.
     *
     * @return IResult
     */
    public function getAll(): IResult;

    /**
     * Получает имена всех колонок таблицы.
     *
     * @return IResult
     */
    public function getColumnNames(): IResult;

    /**
     * Получает все сущности, исключая определённые значения по указанной колонке.
     *
     * @param  string $column
     * @param  array<int|string> $excludedValues
     * @return IResult
     */
    public function getAllExcept(string $column = '', array $excludedValues = []): IResult;

    /**
     * Получает все сущности, где значение указанной колонки входит в список.
     *
     * @param  string $column
     * @param  array<int|string> $values
     * @return IResult
     */
    public function getAllByColumnValues(string $column = '', array $values = []): IResult;
}
