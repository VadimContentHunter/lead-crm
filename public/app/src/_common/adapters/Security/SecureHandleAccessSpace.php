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
    private SecureWrapper $secure;

    public function __construct(
        IAccessSpaceRepository $spaceRepository,
        IAccessGranter $accessGranter,
        ?AccessContext $accessContext
    ) {
        $target = new HandleAccessSpace($spaceRepository);
        $this->secure = new SecureWrapper($target, $accessGranter, $accessContext);
    }

    public function addSpace(string $name, ?string $description = null): ?AccessSpace
    {
        return $this->secure->__call('addSpace', [$name, $description]);
    }

    public function editSpaceById(int $spaceId, ?string $newName = null, ?string $newDescription = null): bool
    {
        return $this->secure->__call('editSpaceById', [$spaceId, $newName, $newDescription]);
    }

    public function editSpaceByName(string $spaceName, ?string $newName = null, ?string $newDescription = null): bool
    {
        return $this->secure->__call('editSpaceByName', [$spaceName, $newName, $newDescription]);
    }

    public function deleteSpace(int $spaceId): bool
    {
        return $this->secure->__call('deleteSpace', [$spaceId]);
    }

    public function getSpaceById(int $spaceId): ?AccessSpace
    {
        return $this->secure->__call('getSpaceById', [$spaceId]);
    }

    public function getSpaceByName(string $name): ?AccessSpace
    {
        return $this->secure->__call('getSpaceByName', [$name]);
    }

    public function getAllSpaces(string $column = '', array $values = []): array
    {
        return $this->secure->__call('getAllSpaces', [$column, $values]);
    }
}
