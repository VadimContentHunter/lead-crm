<?php

namespace crm\src\components\LeadManagement;

use Throwable;
use crm\src\_common\interfaces\IValidation;
use crm\src\components\LeadManagement\_entities\Lead;
use crm\src\components\LeadManagement\_common\DTOs\LeadInputDto;
use crm\src\components\LeadManagement\_common\adapters\LeadResult;
use crm\src\components\LeadManagement\_common\interfaces\ILeadResult;
use crm\src\components\LeadManagement\_common\interfaces\ILeadRepository;
use crm\src\components\LeadManagement\_common\interfaces\ILeadUserRepository;
use crm\src\components\LeadManagement\_common\interfaces\ILeadSourceRepository;
use crm\src\components\LeadManagement\_common\interfaces\ILeadStatusRepository;
use crm\src\components\LeadManagement\_exceptions\LeadManagementException;

class CreateLead
{
    public function __construct(
        private ILeadRepository $repository,
        private ILeadSourceRepository $sourceRepository,
        private ILeadStatusRepository $statusRepository,
        private ILeadUserRepository $userRepository,
        private IValidation $validator,
    ) {
    }

    public function execute(LeadInputDto $dto): ILeadResult
    {
        $validationResult = $this->validator->validate($dto);
        if (!$validationResult->isValid()) {
            return LeadResult::failure(
                new LeadManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        $source = $dto->sourceId !== null
            ? $this->sourceRepository->getById($dto->sourceId)
            : null;

        $status = $dto->statusId !== null
            ? $this->statusRepository->getById($dto->statusId)
            : null;

        $accountManager = $dto->accountManagerId !== null
            ? $this->userRepository->getById($dto->accountManagerId)
            : null;

        $lead = new Lead(
            id: null,
            fullName: $dto->fullName,
            contact: $dto->contact,
            address: $dto->address,
            source: $source,
            status: $status,
            accountManager: $accountManager,
            createdAt: null,
        );

        try {
            $newId = $this->repository->save($lead);

            if ($newId === null || $newId <= 0) {
                return LeadResult::failure(
                    new LeadManagementException('Не удалось создать лид')
                );
            }

            $lead->id = $newId;

            return LeadResult::success($lead);
        } catch (Throwable $e) {
            return LeadResult::failure($e);
        }
    }
}
