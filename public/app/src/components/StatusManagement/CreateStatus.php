<?php

namespace crm\src\components\StatusManagement;

use crm\src\_common\interfaces\IValidation;
use crm\src\components\StatusManagement\_entities\Status;
use crm\src\components\StatusManagement\_common\adapters\StatusResult;
use crm\src\components\StatusManagement\_common\interfaces\IStatusResult;
use crm\src\components\StatusManagement\_common\interfaces\IStatusRepository;
use crm\src\components\StatusManagement\_exceptions\StatusManagementException;

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
     * @throws StatusManagementException Если валидация не пройдена или источник не сохранён.
     * @throws \Throwable В случае неожиданных ошибок при сохранении источника.
     */
    public function execute(string $title): IStatusResult
    {
        // Валидация - здесь можно валидировать просто title
        $validationResult = $this->validator->validateArray(['title' => $title]);

        if (!$validationResult->isValid()) {
            return StatusResult::failure(
                new StatusManagementException(implode('; ', $validationResult->getErrors()))
            );
        }

        $Status = new Status($title);

        try {
            $StatusId = $this->StatusRepository->save($Status);
            if (!is_int($StatusId) || $StatusId <= 0) {
                throw new StatusManagementException('Источник не сохранён');
            }
            $Status->id = $StatusId;
            return StatusResult::success($Status);
        } catch (\Throwable $e) {
            return StatusResult::failure($e);
        }
    }
}
