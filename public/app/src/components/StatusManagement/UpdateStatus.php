<?php

namespace crm\src\components\StatusManagement;

use Throwable;
use crm\src\_common\interfaces\IValidation;
use crm\src\components\StatusManagement\_entities\Status;
use crm\src\components\StatusManagement\_common\adapters\StatusResult;
use crm\src\components\StatusManagement\_common\interfaces\IStatusResult;
use crm\src\components\StatusManagement\_common\interfaces\IStatusRepository;
use crm\src\components\StatusManagement\_exceptions\StatusManagementException;

class UpdateStatus
{
    public function __construct(
        private IStatusRepository $StatusRepository,
        private IValidation $validator,
    ) {
    }

    /**
     * Обновляет источник на основе объекта Status.
     *
     * @param  Status $Status Объект Status с обновлёнными данными (id обязателен).
     * @return IStatusResult Результат операции: успешный с обновлённым Status или с ошибкой.
     */
    public function execute(Status $Status): IStatusResult
    {
        $validationResult = $this->validator->validate($Status);

        if (!$validationResult->isValid()) {
            return StatusResult::failure(
                new StatusManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        try {
            $updatedId = $this->StatusRepository->update($Status);

            if ($updatedId === null || $updatedId <= 0) {
                return StatusResult::failure(
                    new StatusManagementException('Не удалось обновить источник')
                );
            }

            return StatusResult::success($Status);
        } catch (Throwable $e) {
            return StatusResult::failure($e);
        }
    }
}
