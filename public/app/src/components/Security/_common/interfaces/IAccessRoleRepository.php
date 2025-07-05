<?php

namespace crm\src\components\Security\_common\interfaces;

use crm\src\_common\interfaces\IRepository;
use crm\src\components\Security\_entities\AccessRole;

/**
 * @extends IRepository<AccessRole>
 */
interface IAccessRoleRepository extends IRepository
{
    public function getByName(string $name): ?AccessRole;
}
