<?php

namespace crm\src\components\StatusManagement\_usecases;

use Throwable;
use crm\src\_common\interfaces\IValidation;
use crm\src\components\StatusManagement\_entities\Status;
use crm\src\components\StatusManagement\_common\adapters\StatusResult;
use crm\src\components\StatusManagement\_common\interfaces\IStatusResult;
use crm\src\components\StatusManagement\_common\interfaces\IStatusRepository;
use crm\src\components\StatusManagement\_exceptions\StatusManagementException;

class GetStatus
{
    public function __construct(
        private IStatusRepository $repository,
        private IValidation $validator,
    ) {
    }

    /**
     * Получает источник по ID.
     */
    public function getById(int $id): IStatusResult
    {
        try {
            $status = $this->repository->getById($id);
            if ($status === null) {
                return StatusResult::failure(
                    new StatusManagementException("Статус с ID {$id} не найден")
                );
            }

            return StatusResult::success($status);
        } catch (Throwable $e) {
            return StatusResult::failure($e);
        }
    }

    /**
     * Получает источник по названию.
     */
    public function getByTitle(string $title): IStatusResult
    {
        try {
            $status = $this->repository->getByTitle($title);
            if ($status === null) {
                return StatusResult::failure(
                    new StatusManagementException("Статус с названием {$title} не найден")
                );
            }

            return StatusResult::success($status);
        } catch (Throwable $e) {
            return StatusResult::failure($e);
        }
    }

    /**
     * Получает все источники.
     */
    public function getAll(): IStatusResult
    {
        try {
            return StatusResult::success($this->repository->getAll());
        } catch (Throwable $e) {
            return StatusResult::failure($e);
        }
    }

    /**
     * Получает источник по объекту Status.
     */
    public function getByStatus(Status $Status): IStatusResult
    {
        $validationResult = $this->validator->validate($Status);
        if (!$validationResult->isValid()) {
            return StatusResult::failure(
                new StatusManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

         return  isset($Status->id) && $Status->id > 0
            ? $this->getById($Status->id)
            : $this->getByTitle($Status->title);
    }
}
