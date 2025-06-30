<?php

namespace crm\src\components\StatusesManagement\_usecases;

use Throwable;
use crm\src\_common\interfaces\IValidation;
use crm\src\components\StatusesManagement\_entities\Status;
use crm\src\components\StatusesManagement\_common\adapters\StatusResult;
use crm\src\components\StatusesManagement\_common\interfaces\IStatusResult;
use crm\src\components\StatusesManagement\_common\interfaces\IStatusRepository;
use crm\src\components\StatusesManagement\_exceptions\StatusesManagementException;

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
            $Status = $this->repository->getById($id);
            return $Status
                ? StatusResult::success($Status)
                : StatusResult::success(null); // можно вернуть failure, если нужно строго
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
            $Status = $this->repository->getByTitle($title);
            return $Status
                ? StatusResult::success($Status)
                : StatusResult::success(null);
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
            $Statuss = $this->repository->getAll();
            return StatusResult::success($Statuss);
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
                new StatusesManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

         return  isset($Status->id) && $Status->id > 0
            ? $this->getById($Status->id)
            : $this->getByTitle($Status->title);
    }
}
