<?php

namespace crm\src\components\DepositManagement;

use Throwable;
use crm\src\_common\interfaces\IValidation;
use crm\src\components\DepositManagement\_entities\Deposit;
use crm\src\components\DepositManagement\_common\adapters\DepositResult;
use crm\src\components\DepositManagement\_common\interfaces\IDepositResult;
use crm\src\components\DepositManagement\_common\interfaces\IDepositRepository;
use crm\src\components\DepositManagement\_exceptions\DepositManagementException;

class CreateDeposit
{
    public function __construct(
        private IDepositRepository $repository,
        private IValidation $validator,
    ) {
    }

    /**
     * Создаёт новый депозит.
     *
     * @param  Deposit $deposit Объект Deposit с необходимыми данными (leadId, sum и т.д.).
     * @return IDepositResult Результат операции: успешный с созданным Deposit или с ошибкой.
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
            $depositId = $this->repository->save($deposit);

            if ($depositId === null || $depositId <= 0) {
                return DepositResult::failure(
                    new DepositManagementException('Не удалось создать депозит')
                );
            }

            $deposit->id = $depositId;

            return DepositResult::success($deposit);
        } catch (Throwable $e) {
            return DepositResult::failure($e);
        }
    }
}
