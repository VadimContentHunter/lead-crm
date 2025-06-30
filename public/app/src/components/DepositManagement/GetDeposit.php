<?php

namespace crm\src\components\DepositManagement;

use Throwable;
use crm\src\_common\interfaces\IValidation;
use crm\src\components\DepositManagement\_entities\Deposit;
use crm\src\components\DepositManagement\_common\adapters\DepositResult;
use crm\src\components\DepositManagement\_common\interfaces\IDepositResult;
use crm\src\components\DepositManagement\_common\interfaces\IDepositRepository;
use crm\src\components\DepositManagement\_exceptions\DepositManagementException;

class GetDeposit
{
    public function __construct(
        private IDepositRepository $repository,
        private IValidation $validator,
    ) {
    }

    /**
     * Получает депозит по ID.
     *
     * @param  int $id
     * @return IDepositResult
     */
    public function getById(int $id): IDepositResult
    {
        try {
            $deposit = $this->repository->getById($id);
            if ($deposit === null) {
                return DepositResult::failure(
                    new DepositManagementException("Депозит с ID {$id} не найден")
                );
            }

            return DepositResult::success($deposit);
        } catch (Throwable $e) {
            return DepositResult::failure($e);
        }
    }


    /**
     * Получает депозит по leadId.
     *
     * @param  int $leadId
     * @return IDepositResult
     */
    public function getByLeadId(int $leadId): IDepositResult
    {
        try {
            $deposit = $this->repository->getByLeadId($leadId);
            if ($deposit === null) {
                return DepositResult::failure(
                    new DepositManagementException("Депозит с leadId {$leadId} не найден")
                );
            }

            return DepositResult::success($deposit);
        } catch (Throwable $e) {
            return DepositResult::failure($e);
        }
    }


    /**
     * Получает все депозиты.
     *
     * @return IDepositResult
     */
    public function getAll(): IDepositResult
    {
        try {
            $deposits = $this->repository->getAll();
            return DepositResult::success($deposits);
        } catch (Throwable $e) {
            return DepositResult::failure($e);
        }
    }

    /**
     * Получает депозит по объекту Deposit.
     *
     * @param  Deposit $deposit
     * @return IDepositResult
     */
    public function getByDeposit(Deposit $deposit): IDepositResult
    {
        $validationResult = $this->validator->validate($deposit);
        if (!$validationResult->isValid()) {
            return DepositResult::failure(
                new DepositManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        return isset($deposit->id) && $deposit->id > 0
            ? $this->getById($deposit->id)
            : $this->getByLeadId($deposit->leadId);
    }
}
