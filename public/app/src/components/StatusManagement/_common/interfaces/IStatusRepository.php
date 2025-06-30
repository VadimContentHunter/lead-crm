<?php

namespace crm\src\components\StatusesManagement\_common\interfaces;

use crm\src\components\StatusesManagement\_entities\Status;

interface IStatusRepository
{
    /**
     * @return int|null Возвращает id сохраненного источника
     */
    public function save(Status $Status): ?int;

    /**
     * @return int|null Возвращает id удаленного источника по названию
     */
    public function deleteByTitle(string $title): ?int;

    /**
     * @return int|null Возвращает id удаленного источника по id
     */
    public function deleteById(int $id): ?int;

    /**
     * @return int|null Возвращает id обновленного источника
     */
    public function update(Status $Status): ?int;

    /**
     * @return Status|null Возвращает источник по названию
     */
    public function getByTitle(string $title): ?Status;

    /**
     * @return Status[]
     */
    public function getAll(): array;

    /**
     * @return Status|null Возвращает источник по id
     */
    public function getById(int $id): ?Status;
}
