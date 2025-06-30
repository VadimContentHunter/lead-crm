<?php

namespace crm\src\components\SourcesManagement\_usecases;

use Throwable;
use InvalidArgumentException;
use crm\src\_common\interfaces\IValidation;
use crm\src\components\SourcesManagement\_entities\Source;
use crm\src\components\SourcesManagement\_common\adapters\SourceResult;
use crm\src\components\SourcesManagement\_common\interfaces\ISourceResult;
use crm\src\components\SourcesManagement\_common\interfaces\ISourceRepository;
use crm\src\components\SourcesManagement\_exceptions\SourcesManagementException;

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
            return $source
                ? SourceResult::success($source)
                : SourceResult::success(null); // можно вернуть failure, если нужно строго
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
            return $source
                ? SourceResult::success($source)
                : SourceResult::success(null);
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
                new SourcesManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

         return  isset($source->id) && $source->id > 0
            ? $this->getById($source->id)
            : $this->getByTitle($source->title);
    }
}
