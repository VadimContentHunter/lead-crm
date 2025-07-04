<?php

namespace crm\src\components\DepositManagement;

use Throwable;
use crm\src\_common\interfaces\IValidation;
use crm\src\components\DepositManagement\_entities\Deposit;
use crm\src\components\DepositManagement\_common\adapters\DepositResult;
use crm\src\components\DepositManagement\_common\interfaces\IDepositResult;
use crm\src\components\DepositManagement\_common\interfaces\IDepositRepository;
use crm\src\components\DepositManagement\_exceptions\DepositManagementException;

class UpdateDeposit
{
    public function __construct(
        private IDepositRepository $repository,
        private IValidation $validator,
    ) {
    }

    /**
     * Обновляет депозит на основе объекта Deposit.
     *
     * @param  Deposit $deposit Объект с обновлёнными данными (валидируется валидатором).
     * @return IDepositResult Результат операции: успешный с обновлённым Deposit или с ошибкой.
     */
    public function execute(Deposit $deposit): IDepositResult
    {
        $validationResult = $this->validator->validate($deposit);

        if (!$validationResult->isValid()) {
            return DepositResult::failure(
                new DepositManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        try {
            $updatedId = $this->repository->update($deposit);

            if ($updatedId === null || $updatedId <= 0) {
                return DepositResult::failure(
                    new DepositManagementException('Не удалось обновить депозит')
                );
            }

            return DepositResult::success($deposit);
        } catch (Throwable $e) {
            return DepositResult::failure($e);
        }
    }

    /**
     * Обновляет депозит по leadId.
     */
    public function executeByLeadId(Deposit $deposit): IDepositResult
    {
        $validationResult = $this->validator->validate($deposit);

        if (!$validationResult->isValid()) {
            return DepositResult::failure(
                new DepositManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        if (empty($deposit->leadId) || $deposit->leadId <= 0) {
            return DepositResult::failure(
                new DepositManagementException('Параметр leadId обязателен для обновления депозита')
            );
        }

        try {
            $updated = $this->repository->updateByLeadId($deposit);

            if (!$updated) {
                return DepositResult::failure(
                    new DepositManagementException('Не удалось обновить депозит по leadId')
                );
            }

            return DepositResult::success($deposit);
        } catch (Throwable $e) {
            return DepositResult::failure($e);
        }
    }
}
