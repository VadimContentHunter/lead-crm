<?php

namespace crm\src\_common\adapters\Security;

use crm\src\components\Security\SecureWrapper;
use crm\src\components\Security\_entities\AccessSpace;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_handlers\HandleAccessSpace;
use crm\src\components\Security\_common\interfaces\IAccessGranter;
use crm\src\components\Security\_common\interfaces\IHandleAccessSpace;
use crm\src\components\Security\_common\interfaces\IAccessSpaceRepository;

class SecureHandleAccessSpace implements IHandleAccessSpace
{
    /**
     * @var HandleAccessSpace
     */
    private SecureWrapper $secure;

    public function __construct(
        IAccessSpaceRepository $spaceRepository,
        IAccessGranter $accessGranter,
        ?AccessContext $accessContext
    ) {
        // Создаем реальный target
        $target = new HandleAccessSpace($spaceRepository);

        // Создаем оборачивающий объект
        $this->secure = new SecureWrapper($target, $accessGranter, $accessContext);

        // Оборачиваем его
        // $this->secureHandleAccessSpace = new SecureWrapper($target, $accessGranter, $accessContext);
    }

    /**
     * Добавление нового пространства.
     */
    public function addSpace(string $name, ?string $description = null): ?AccessSpace
    {
        return $this->secure->addSpace($name, $description);
    }

    /**
     * Редактирование пространства по ID.
     */
    public function editSpaceById(int $spaceId, ?string $newName = null, ?string $newDescription = null): bool
    {
        return $this->secure->editSpaceById($spaceId, $newName, $newDescription);
    }

    /**
     * Редактирование пространства по имени (этот метод остался локальным).
     */
    public function editSpaceByName(string $spaceName, ?string $newName = null, ?string $newDescription = null): bool
    {
        return $this->secure->editSpaceByName($spaceName, $newName, $newDescription);
    }

    public function deleteSpace(int $spaceId): bool
    {
        return $this->secure->deleteSpace($spaceId);
    }

    public function getSpaceById(int $spaceId): ?AccessSpace
    {
        return $this->secure->getSpaceById($spaceId);
    }

    public function getSpaceByName(string $name): ?AccessSpace
    {
        return $this->secure->getSpaceByName($name);
    }

    public function getAllSpaces(string $column = '', array $values = []): array
    {
        return $this->secure->getAllSpaces($column, $values);
    }
}
