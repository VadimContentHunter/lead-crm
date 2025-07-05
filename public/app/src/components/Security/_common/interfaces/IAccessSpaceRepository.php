<?php

namespace crm\src\components\Security\_common\interfaces;

use crm\src\_common\interfaces\IRepository;
use crm\src\components\Security\_entities\AccessSpace;

/**
 * @extends IRepository<AccessSpace>
 */
interface IAccessSpaceRepository extends IRepository
{
    public function getByName(string $name): ?AccessSpace;
}
