<?php

namespace crm\src\components\SourcesManagement\_common\interfaces;

use crm\src\components\SourcesManagement\_entities\Source;

interface ISourceRepository
{
    /**
     * @return int|null Возвращает id сохраненного источника
     */
    public function save(Source $source): ?int;

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
    public function update(Source $source): ?int;

    /**
     * @return Source|null Возвращает источник по названию
     */
    public function getByTitle(string $title): ?Source;

    /**
     * @return Source[]
     */
    public function getAll(): array;

    /**
     * @return Source|null Возвращает источник по id
     */
    public function getById(int $id): ?Source;
}
