<?php

namespace crm\src\components\StatusManagement;

use Throwable;
use crm\src\_common\interfaces\IValidation;
use crm\src\components\StatusManagement\_entities\Status;
use crm\src\components\StatusManagement\_common\adapters\StatusResult;
use crm\src\components\StatusManagement\_common\interfaces\IStatusResult;
use crm\src\components\StatusManagement\_common\interfaces\IStatusRepository;
use crm\src\components\StatusManagement\_exceptions\StatusManagementException;

class DeleteStatus
{
    public function __construct(
        private IStatusRepository $StatusRepository,
        private IValidation $validator,
    ) {
    }

    /**
     * Удаляет источник по ID.
     *
     * @param  int $id ID источника.
     * @return IStatusResult Результат операции: успешный с ID удалённого источника или с ошибкой.
     */
    public function executeById(int $id): IStatusResult
    {
        $validationResult = $this->validator->validateArray(['id' => $id]);

        if (!$validationResult->isValid()) {
            return StatusResult::failure(
                new StatusManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        try {
            $deletedId = $this->StatusRepository->deleteById($id);

            if ($deletedId === null) {
                return StatusResult::failure(
                    new StatusManagementException("Источник с ID {$id} не найден или не удалён")
                );
            }

            return StatusResult::success($deletedId);
        } catch (\Throwable $e) {
            return StatusResult::failure($e);
        }
    }

    /**
     * Удаляет источник по названию.
     *
     * @param  string $title Название источника.
     * @return IStatusResult Результат операции: успешный с ID удалённого источника или с ошибкой.
     */
    public function executeByTitle(string $title): IStatusResult
    {
        $validationResult = $this->validator->validateArray(['title' => $title]);

        if (!$validationResult->isValid()) {
            return StatusResult::failure(
                new StatusManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        try {
            $deletedId = $this->StatusRepository->deleteByTitle($title);

            if ($deletedId === null) {
                return StatusResult::failure(
                    new StatusManagementException("Источник с названием '{$title}' не найден или не удалён")
                );
            }

            return StatusResult::success($deletedId);
        } catch (\Throwable $e) {
            return StatusResult::failure($e);
        }
    }

    /**
     * Удаляет источник по объекту Status.
     *
     * Попытка удалить по id, если он есть и > 0,
     * иначе — по названию.
     *
     * @param  Status $Status Источник с заполненными полями id и/или title.
     * @return IStatusResult Результат операции: успешный с ID удалённого источника или с ошибкой.
     */
    public function executeByStatus(Status $Status): IStatusResult
    {
        $validationResult = $this->validator->validate($Status);

        if (!$validationResult->isValid()) {
            return StatusResult::failure(
                new StatusManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        try {
            return  isset($Status->id) && $Status->id > 0
            ? $this->executeById($Status->id)
            : $this->executeByTitle($Status->title);
        } catch (Throwable $e) {
            return StatusResult::failure($e);
        }
    }
}
