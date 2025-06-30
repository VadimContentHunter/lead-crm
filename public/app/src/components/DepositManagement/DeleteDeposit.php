<?php

namespace crm\src\components\DepositManagement;

use Throwable;
use crm\src\_common\interfaces\IValidation;
use crm\src\components\DepositManagement\_common\adapters\DepositResult;
use crm\src\components\DepositManagement\_common\interfaces\IDepositResult;
use crm\src\components\DepositManagement\_common\interfaces\IDepositRepository;
use crm\src\components\DepositManagement\_exceptions\DepositManagementException;

class DeleteDeposit
{
    public function __construct(
        private IDepositRepository $repository,
        private IValidation $validator,
    ) {
    }

    /**
     * Удаляет депозит по ID.
     *
     * @param  int $id ID депозита.
     * @return IDepositResult Результат операции: успешный с ID удалённого депозита или с ошибкой.
     */
    public function executeById(int $id): IDepositResult
    {
        $validationResult = $this->validator->validateArray(['id' => $id]);

        if (!$validationResult->isValid()) {
            return DepositResult::failure(
                new DepositManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        try {
            $deletedId = $this->repository->deleteById($id);

            if ($deletedId === null) {
                return DepositResult::failure(
                    new DepositManagementException("Депозит с ID {$id} не найден или не удалён")
                );
            }

            return DepositResult::success($deletedId);
        } catch (Throwable $e) {
            return DepositResult::failure($e);
        }
    }

    /**
     * Удаляет депозит по leadId.
     *
     * @param  int $leadId ID лида.
     * @return IDepositResult Результат операции: успешный с ID удалённого депозита или с ошибкой.
     */
    public function executeByLeadId(int $leadId): IDepositResult
    {
        $validationResult = $this->validator->validateArray(['leadId' => $leadId]);

        if (!$validationResult->isValid()) {
            return DepositResult::failure(
                new DepositManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        try {
            $deletedId = $this->repository->deleteByLeadId($leadId);

            if ($deletedId === null) {
                return DepositResult::failure(
                    new DepositManagementException("Депозит с leadId {$leadId} не найден или не удалён")
                );
            }

            return DepositResult::success($deletedId);
        } catch (Throwable $e) {
            return DepositResult::failure($e);
        }
    }
}
