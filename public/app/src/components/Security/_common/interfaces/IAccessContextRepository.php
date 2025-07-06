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
}
