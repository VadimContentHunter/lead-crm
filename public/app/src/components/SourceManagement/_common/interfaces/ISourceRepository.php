<?php

namespace crm\src\components\SourceManagement\_common\interfaces;

use crm\src\_common\interfaces\IRepository;
use crm\src\components\SourceManagement\_entities\Source;

/**
 * @extends IRepository<Source>
 */
interface ISourceRepository extends IRepository
{
    /**
     * @return int|null Возвращает id удаленного источника по названию
     */
    public function deleteByTitle(string $title): ?int;

    /**
     * @return Source|null Возвращает источник по названию
     */
    public function getByTitle(string $title): ?Source;
}
