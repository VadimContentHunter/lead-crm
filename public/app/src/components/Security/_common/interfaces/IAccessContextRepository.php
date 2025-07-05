<?php

namespace crm\src\components\Security\_common\interfaces;

use crm\src\_common\interfaces\IRepository;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\SourceManagement\_entities\Source;

/**
 * @extends IRepository<Source>
 */
interface IAccessContextRepository extends IRepository
{
    public function getBySessionHash(string $hash): ?AccessContext;

    public function deleteBySessionHash(string $hash): bool;
}
