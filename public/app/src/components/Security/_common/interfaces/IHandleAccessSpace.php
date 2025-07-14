<?php

namespace crm\src\components\Security\_common\interfaces;

use crm\src\_common\interfaces\IRepository;
use crm\src\components\Security\_entities\AccessSpace;

interface IHandleAccessSpace
{
    /**
     * Добавление нового пространства.
     */
    public function addSpace(string $name, ?string $description = null): ?AccessSpace;

    /**
     * Редактирование пространства по ID.
     */
    public function editSpaceById(int $spaceId, ?string $newName = null, ?string $newDescription = null): bool;

    /**
     * Редактирование пространства по имени.
     */
    public function editSpaceByName(string $spaceName, ?string $newName = null, ?string $newDescription = null): bool;

    /**
     * Удаление пространства по ID.
     */
    public function deleteSpace(int $spaceId): bool;

    /**
     * Получение пространства по ID.
     */
    public function getSpaceById(int $spaceId): ?AccessSpace;

    /**
     * Получение пространства по имени.
     */
    public function getSpaceByName(string $name): ?AccessSpace;

    /**
     * @param array<int|string> $values
     *
     * @return AccessSpace[]
     */
    public function getAllSpaces(string $column = '', array $values = []): array;
}
