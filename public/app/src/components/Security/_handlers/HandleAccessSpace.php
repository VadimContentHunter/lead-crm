<?php

namespace crm\src\components\Security\_handlers;

use RuntimeException;
use crm\src\components\Security\_entities\AccessSpace;
use crm\src\components\Security\_common\interfaces\IHandleAccessSpace;
use crm\src\components\Security\_common\interfaces\IAccessSpaceRepository;

class HandleAccessSpace implements IHandleAccessSpace
{
    public function __construct(
        private IAccessSpaceRepository $spaceRepository
    ) {
    }

    /**
     * Добавление нового пространства.
     */
    public function addSpace(string $name, ?string $description = null): ?AccessSpace
    {
        $space = new AccessSpace(name: $name, description: $description);
        $savedId = $this->spaceRepository->save($space);

        if ($savedId <= 0) {
            return null;
        }

        $space->id = $savedId;
        return $space;
    }

    /**
     * Редактирование пространства по ID.
     */
    public function editSpaceById(int $spaceId, ?string $newName = null, ?string $newDescription = null): bool
    {
        $data = ['id' => $spaceId];
        if ($newName !== null) {
            $data['name'] = $newName;
        }
        if ($newDescription !== null) {
            $data['description'] = $newDescription;
        }

        return $this->spaceRepository->update($data) !== null;
    }

    /**
     * Редактирование пространства по имени.
     */
    public function editSpaceByName(string $spaceName, ?string $newName = null, ?string $newDescription = null): bool
    {
        $space = $this->spaceRepository->getByName($spaceName);
        if ($space === null) {
            return false;
        }

        $data = ['id' => $space->id];
        if ($newName !== null) {
            $data['name'] = $newName;
        }
        if ($newDescription !== null) {
            $data['description'] = $newDescription;
        }

        return $this->spaceRepository->update($data) !== null;
    }

    /**
     * Удаление пространства по ID.
     */
    public function deleteSpace(int $spaceId): bool
    {
        return $this->spaceRepository->deleteById($spaceId) === null ? false : true;
    }

    /**
     * Получение пространства по ID.
     */
    public function getSpaceById(int $spaceId): ?AccessSpace
    {
        return $this->spaceRepository->getById($spaceId);
    }

    /**
     * Получение пространства по имени.
     */
    public function getSpaceByName(string $name): ?AccessSpace
    {
        return $this->spaceRepository->getByName($name);
    }

    /**
     * @return AccessSpace[]
     */
    public function getAllSpaces(string $column = '', array $values = []): array
    {
        return $this->spaceRepository->getAllByColumnValues($column, $values);
    }
}
