<?php

namespace crm\src\components\StatusesManagement;

use crm\src\_common\interfaces\IValidation;
use crm\src\components\StatusesManagement\_entities\Status;
use crm\src\components\StatusesManagement\_common\adapters\StatusResult;
use crm\src\components\StatusesManagement\_common\interfaces\IStatusResult;
use crm\src\components\StatusesManagement\_common\interfaces\IStatusRepository;
use crm\src\components\StatusesManagement\_exceptions\StatusesManagementException;

class CreateStatus
{
    public function __construct(
        private IStatusRepository $StatusRepository,
        private IValidation $validator,
    ) {
    }

    /**
     * Создаёт новый источник на основе параметров.
     *
     * Проводит валидацию данных, сохраняет источник в репозиторий
     * и возвращает результат операции с объектом Status или ошибкой.
     *
     * @param  string $title Название источника
     * @return IStatusResult Результат операции: успешный с Status или неуспешный с ошибкой.
     *
     * @throws StatusesManagementException Если валидация не пройдена или источник не сохранён.
     * @throws \Throwable В случае неожиданных ошибок при сохранении источника.
     */
    public function execute(string $title): IStatusResult
    {
        // Валидация - здесь можно валидировать просто title
        $validationResult = $this->validator->validateArray(['title' => $title]);

        if (!$validationResult->isValid()) {
            return StatusResult::failure(
                new StatusesManagementException(implode('; ', $validationResult->getErrors()))
            );
        }

        $Status = new Status($title);

        try {
            $StatusId = $this->StatusRepository->save($Status);
            if (!is_int($StatusId) || $StatusId <= 0) {
                throw new StatusesManagementException('Источник не сохранён');
            }
            $Status->id = $StatusId;
            return StatusResult::success($Status);
        } catch (\Throwable $e) {
            return StatusResult::failure($e);
        }
    }
}
