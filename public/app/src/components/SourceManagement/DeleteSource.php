<?php

namespace crm\src\components\SourceManagement;

use Throwable;
use crm\src\_common\interfaces\IValidation;
use crm\src\components\SourceManagement\_entities\Source;
use crm\src\components\SourceManagement\_common\adapters\SourceResult;
use crm\src\components\SourceManagement\_common\interfaces\ISourceResult;
use crm\src\components\SourceManagement\_common\interfaces\ISourceRepository;
use crm\src\components\SourceManagement\_exceptions\SourceManagementException;

class DeleteSource
{
    public function __construct(
        private ISourceRepository $sourceRepository,
        private IValidation $validator,
    ) {
    }

    /**
     * Удаляет источник по ID.
     *
     * @param  int $id ID источника.
     * @return ISourceResult Результат операции: успешный с ID удалённого источника или с ошибкой.
     */
    public function executeById(int $id): ISourceResult
    {
        try {
            $deletedId = $this->sourceRepository->deleteById($id) ?? -1;

            if ($deletedId < 0) {
                return SourceResult::failure(
                    new SourceManagementException("Источник с ID {$id} не найден или не удалён")
                );
            }

            return SourceResult::success($deletedId);
        } catch (\Throwable $e) {
            return SourceResult::failure($e);
        }
    }

    /**
     * Удаляет источник по названию.
     *
     * @param  string $title Название источника.
     * @return ISourceResult Результат операции: успешный с ID удалённого источника или с ошибкой.
     */
    public function executeByTitle(string $title): ISourceResult
    {
        $validationResult = $this->validator->validateArray(['title' => $title]);

        if (!$validationResult->isValid()) {
            return SourceResult::failure(
                new SourceManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        try {
            $deletedId = $this->sourceRepository->deleteByTitle($title);

            if ($deletedId === null) {
                return SourceResult::failure(
                    new SourceManagementException("Источник с названием '{$title}' не найден или не удалён")
                );
            }

            return SourceResult::success($deletedId);
        } catch (\Throwable $e) {
            return SourceResult::failure($e);
        }
    }

    /**
     * Удаляет источник по объекту Source.
     *
     * Попытка удалить по id, если он есть и > 0,
     * иначе — по названию.
     *
     * @param  Source $source Источник с заполненными полями id и/или title.
     * @return ISourceResult Результат операции: успешный с ID удалённого источника или с ошибкой.
     */
    public function executeBySource(Source $source): ISourceResult
    {
        $validationResult = $this->validator->validate($source);

        if (!$validationResult->isValid()) {
            return SourceResult::failure(
                new SourceManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        try {
            return  isset($source->id) && $source->id > 0
            ? $this->executeById($source->id)
            : $this->executeByTitle($source->title);
        } catch (Throwable $e) {
            return SourceResult::failure($e);
        }
    }
}
