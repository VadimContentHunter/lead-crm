<?php

namespace crm\src\components\LeadManagement;

use Throwable;
use crm\src\_common\interfaces\IValidation;
use crm\src\components\LeadManagement\_common\interfaces\ILeadRepository;
use crm\src\components\LeadManagement\_common\interfaces\ILeadSourceRepository;
use crm\src\components\LeadManagement\_common\interfaces\ILeadStatusRepository;
use crm\src\components\LeadManagement\_common\interfaces\ILeadAccountManagerRepository;
use crm\src\components\LeadManagement\_common\DTOs\LeadInputDto;
use crm\src\components\LeadManagement\_entities\Lead;
use crm\src\components\LeadManagement\_common\interfaces\ILeadResult;
use crm\src\components\LeadManagement\_common\adapters\LeadResult;
use crm\src\components\LeadManagement\_exceptions\LeadManagementException;

class UpdateLead
{
    public function __construct(
        private ILeadRepository $repository,
        private ILeadSourceRepository $sourceRepository,
        private ILeadStatusRepository $statusRepository,
        private ILeadAccountManagerRepository $accManagerRepository,
        private IValidation $validator,
    ) {
    }

    public function execute(LeadInputDto $dto): ILeadResult
    {
        // Валидация входных данных
        $validationResult = $this->validator->validate($dto);
        if (!$validationResult->isValid()) {
            return LeadResult::failure(
                new LeadManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        try {
            // Получаем существующий лид для проверки
            $existingLead = $this->repository->getById($dto->id);
            if ($existingLead === null) {
                return LeadResult::failure(new LeadManagementException('Лид не найден'));
            }

            // Получаем связанные сущности по ID, если они заданы
            $source = $dto->sourceId !== null
                ? $this->sourceRepository->getById($dto->sourceId)
                : null;

            $status = $dto->statusId !== null
                ? $this->statusRepository->getById($dto->statusId)
                : null;

            $accountManager = $dto->accountManagerId !== null
                ? $this->accManagerRepository->getById($dto->accountManagerId)
                : null;

            // Создаем объект Lead с обновленными данными
            $lead = new Lead(
                id: $dto->id,
                fullName: $dto->fullName,
                contact: $dto->contact,
                address: $dto->address,
                source: $source,
                status: $status,
                accountManager: $accountManager,
                createdAt: $existingLead->createdAt, // сохраним дату создания
            );

            // Выполняем обновление через репозиторий
            $updatedId = $this->repository->update($lead);

            if ($updatedId === null || $updatedId <= 0) {
                return LeadResult::failure(new LeadManagementException('Не удалось обновить лид'));
            }

            return LeadResult::success($lead);
        } catch (Throwable $e) {
            return LeadResult::failure($e);
        }
    }
}
