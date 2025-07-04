<?php

namespace crm\src\components\SourceManagement;

use Throwable;
use InvalidArgumentException;
use crm\src\_common\interfaces\IValidation;
use crm\src\components\SourceManagement\_entities\Source;
use crm\src\components\SourceManagement\_common\adapters\SourceResult;
use crm\src\components\SourceManagement\_common\interfaces\ISourceResult;
use crm\src\components\SourceManagement\_common\interfaces\ISourceRepository;
use crm\src\components\SourceManagement\_exceptions\SourceManagementException;

class GetSource
{
    public function __construct(
        private ISourceRepository $repository,
        private IValidation $validator,
    ) {
    }

    /**
     * Получает источник по ID.
     */
    public function getById(int $id): ISourceResult
    {
        try {
            $source = $this->repository->getById($id);
            if ($source === null) {
                return SourceResult::failure(
                    new SourceManagementException("Источник с ID {$id} не найден")
                );
            }

            return SourceResult::success($source);
        } catch (Throwable $e) {
            return SourceResult::failure($e);
        }
    }

    /**
     * Получает источник по названию.
     */
    public function getByTitle(string $title): ISourceResult
    {
        try {
            $source = $this->repository->getByTitle($title);

            if ($source === null) {
                return SourceResult::failure(
                    new SourceManagementException("Источник с названием {$title} не найден")
                );
            }

            return SourceResult::success($source);
        } catch (Throwable $e) {
            return SourceResult::failure($e);
        }
    }

    /**
     * Получает все источники.
     */
    public function getAll(): ISourceResult
    {
        try {
            $sources = $this->repository->getAll();
            return SourceResult::success($sources);
        } catch (Throwable $e) {
            return SourceResult::failure($e);
        }
    }

    /**
     * Получает источник по объекту Source.
     */
    public function getBySource(Source $source): ISourceResult
    {
        $validationResult = $this->validator->validate($source);

        if (!$validationResult->isValid()) {
            return SourceResult::failure(
                new SourceManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

         return  isset($source->id) && $source->id > 0
            ? $this->getById($source->id)
            : $this->getByTitle($source->title);
    }

    /**
     * Возвращает названия столбцов таблицы статусов.
     *
     * @param  array<string, string> $renameMap Ключ — оригинальное имя, значение — новое имя
     * @return ISourceResult
     */
    public function executeColumnNames(array $renameMap = []): ISourceResult
    {
        try {
            $columns = $this->repository->getColumnNames();

            if (!empty($renameMap)) {
                $columns = array_map(
                    fn($name) => $renameMap[$name] ?? $name,
                    $columns
                );
            }

            return SourceResult::success($columns);
        } catch (Throwable $e) {
            return SourceResult::failure($e);
        }
    }

    /**
     * @template T
     * @param    callable(Source): T $mapper
     * @return   ISourceResult
     */
    public function executeAllMapped(callable $mapper): ISourceResult
    {
        try {
            $sources = $this->repository->getAll();
            $mapped = array_map($mapper, $sources);

            return SourceResult::success($mapped);
        } catch (Throwable $e) {
            return SourceResult::failure($e);
        }
    }
}
