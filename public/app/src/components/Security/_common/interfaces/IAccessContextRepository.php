<?php

namespace crm\src\components\Security\_common\interfaces;

use crm\src\_common\interfaces\IRepository;
use crm\src\components\Security\_entities\AccessContext;

/**
 * @extends IRepository<AccessContext>
 */
interface IAccessContextRepository extends IRepository
{
    public function getByUserId(int $userId): ?AccessContext;

    public function getBySessionHash(string $hash): ?AccessContext;

    public function deleteBySessionHash(string $hash): bool;

    /**
     * Получить все контексты по spaceId.
     *
     * @param  int $spaceId
     * @return AccessContext[]
     */
    public function getAllBySpaceId(int $spaceId): array;

    /**
     * Получить все контексты по roleId.
     *
     * @param  int $roleId
     * @return AccessContext[]
     */
    public function getAllByRoleId(int $roleId): array;
}
