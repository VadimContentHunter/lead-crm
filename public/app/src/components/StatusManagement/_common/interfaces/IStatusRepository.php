<?php

namespace crm\src\components\StatusManagement\_common\interfaces;

use crm\src\_common\interfaces\IRepository;
use crm\src\components\StatusManagement\_entities\Status;

/**
 * @extends IRepository<Status>
 */
interface IStatusRepository extends IRepository
{
    /**
     * @return int|null Возвращает id удаленного источника по названию
     */
    public function deleteByTitle(string $title): ?int;

    /**
     * @return Status|null Возвращает источник по названию
     */
    public function getByTitle(string $title): ?Status;
}
