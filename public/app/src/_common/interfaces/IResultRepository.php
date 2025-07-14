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
     * @return IResult<int|null>
     */
    public function save(object|array $entity): IResult;

    /**
     * Обновляет сущность или часть полей по id.
     *
     * @param  object|array<string, mixed> $entityOrData
     * @return IResult<int|null>
     */
    public function update(object|array $entityOrData): IResult;

    /**
     * Удаляет по ID.
     *
     * @param  int $id
     * @return IResult<int|null>
     */
    public function deleteById(int $id): IResult;

    /**
     * Получает сущность по ID.
     *
     * @param  int $id
     * @return IResult<TEntity|null>
     */
    public function getById(int $id): IResult;

    /**
     * Получает все сущности.
     *
     * @return IResult<TEntity[]>
     */
    public function getAll(): IResult;

    /**
     * Получает имена всех колонок таблицы.
     *
     * @return IResult<string[]>
     */
    public function getColumnNames(): IResult;

    /**
     * Получает все сущности, исключая определённые значения по указанной колонке.
     *
     * @param  string $column
     * @param  array<int|string> $excludedValues
     * @return IResult<TEntity[]>
     */
    public function getAllExcept(string $column = '', array $excludedValues = []): IResult;

    /**
     * Получает все сущности, где значение указанной колонки входит в список.
     *
     * @param  string $column
     * @param  array<int|string> $values
     * @return IResult<TEntity[]>
     */
    public function getAllByColumnValues(string $column = '', array $values = []): IResult;
}
