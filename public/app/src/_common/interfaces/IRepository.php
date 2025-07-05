<?php

namespace crm\src\_common\interfaces;

/**
 * Универсальный интерфейс для базовых CRUD-операций над сущностями.
 *
 * @template TEntity of object
 */
interface IRepository
{
    /**
     * Сохраняет сущность.
     *
     * @param  TEntity $entity
     * @return int|null ID новой сущности или null при неудаче.
     */
    public function save(object $entity): ?int;

    /**
     * Обновляет сущность или часть полей по id.
     *
     * @param  object|array<string, mixed> $entityOrData Полный объект или массив полей с ключом id.
     * @return int|null ID обновлённой сущности или null при неудаче.
     */
    public function update(object|array $entityOrData): ?int;

    /**
     * Удаляет по ID.
     *
     * @param  int $id
     * @return int|null ID удалённой сущности или null при неудаче.
     */
    public function deleteById(int $id): ?int;

    /**
     * Получает сущность по ID.
     *
     * @param  int $id
     * @return TEntity|null
     */
    public function getById(int $id): ?object;

    /**
     * Возвращает все сущности.
     *
     * @return TEntity[]
     */
    public function getAll(): array;

    /**
     * Возвращает названия колонок из базы данных.
     *
     * @return string[]
     */
    public function getColumnNames(): array;
}
