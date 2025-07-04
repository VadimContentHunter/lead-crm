<?php

namespace crm\src\components\BalanceManagement;

use Throwable;
use crm\src\_common\interfaces\IValidation;
use crm\src\components\BalanceManagement\_common\adapters\BalanceResult;
use crm\src\components\BalanceManagement\_common\interfaces\IBalanceResult;
use crm\src\components\BalanceManagement\_common\interfaces\IBalanceRepository;
use crm\src\components\BalanceManagement\_exceptions\BalanceManagementException;

class DeleteBalance
{
    public function __construct(
        private IBalanceRepository $balanceRepository,
        private IValidation $validator,
    ) {
    }

    /**
     * Удаляет баланс по ID.
     *
     * @param  int $id ID баланса.
     * @return IBalanceResult Результат операции: успешный с ID удалённого баланса или ошибкой.
     */
    public function executeById(int $id): IBalanceResult
    {
        $validationResult = $this->validator->validateArray(['id' => $id]);

        if (!$validationResult->isValid()) {
            return BalanceResult::failure(
                new BalanceManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        try {
            $deletedId = $this->balanceRepository->deleteById($id);

            if ($deletedId === null) {
                return BalanceResult::failure(
                    new BalanceManagementException("Баланс с ID {$id} не найден или не удалён")
                );
            }

            return BalanceResult::success($deletedId);
        } catch (Throwable $e) {
            return BalanceResult::failure($e);
        }
    }

    /**
     * Удаляет баланс по leadId.
     *
     * @param  int $leadId ID лида.
     * @return IBalanceResult Результат операции: успешный с ID удалённого баланса или ошибкой.
     */
    public function executeByLeadId(int $leadId): IBalanceResult
    {
        $validationResult = $this->validator->validateArray(['lead_id' => $leadId]);

        if (!$validationResult->isValid()) {
            return BalanceResult::failure(
                new BalanceManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        try {
            $deletedId = $this->balanceRepository->deleteByLeadId($leadId);

            if ($deletedId === null) {
                return BalanceResult::failure(
                    new BalanceManagementException("Баланс с lead_id {$leadId} не найден или не удалён")
                );
            }

            return BalanceResult::success($deletedId);
        } catch (Throwable $e) {
            return BalanceResult::failure($e);
        }
    }
}
