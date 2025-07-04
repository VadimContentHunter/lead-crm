<?php

namespace crm\src\components\BalanceManagement;

use Throwable;
use crm\src\_common\interfaces\IValidation;
use crm\src\components\BalanceManagement\_entities\Balance;
use crm\src\components\BalanceManagement\_common\adapters\BalanceResult;
use crm\src\components\BalanceManagement\_common\interfaces\IBalanceResult;
use crm\src\components\BalanceManagement\_common\interfaces\IBalanceRepository;
use crm\src\components\BalanceManagement\_exceptions\BalanceManagementException;

class UpdateBalance
{
    public function __construct(
        private IBalanceRepository $balanceRepository,
        private IValidation $validator,
    ) {
    }

    /**
     * Обновляет баланс на основе объекта Balance.
     *
     * @param  Balance $balance Объект Balance с обновлёнными данными (id обязателен).
     * @return IBalanceResult Результат операции: успешный с обновлённым Balance или с ошибкой.
     */
    public function execute(Balance $balance): IBalanceResult
    {
        $validationResult = $this->validator->validate($balance);

        if (!$validationResult->isValid()) {
            return BalanceResult::failure(
                new BalanceManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        try {
            $updatedId = $this->balanceRepository->update($balance);

            if ($updatedId === null || $updatedId <= 0) {
                return BalanceResult::failure(
                    new BalanceManagementException('Не удалось обновить баланс')
                );
            }

            return BalanceResult::success($balance);
        } catch (Throwable $e) {
            return BalanceResult::failure($e);
        }
    }

    /**
     * Обновляет баланс по leadId.
     */
    public function executeByLeadId(Balance $balance): IBalanceResult
    {
        $validationResult = $this->validator->validate($balance);

        if (!$validationResult->isValid()) {
            return BalanceResult::failure(
                new BalanceManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        if (empty($balance->leadId) || $balance->leadId <= 0) {
            return BalanceResult::failure(
                new BalanceManagementException('Параметр leadId обязателен для обновления баланса')
            );
        }

        try {
            $updatedId = $this->balanceRepository->updateByLeadId($balance);

            if (!$updatedId) {
                return BalanceResult::failure(
                    new BalanceManagementException('Не удалось обновить баланс по leadId')
                );
            }

            return BalanceResult::success($balance);
        } catch (Throwable $e) {
            return BalanceResult::failure($e);
        }
    }
}
