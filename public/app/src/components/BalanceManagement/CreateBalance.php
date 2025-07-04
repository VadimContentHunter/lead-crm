<?php

namespace crm\src\components\BalanceManagement;

use Throwable;
use crm\src\_common\interfaces\IValidation;
use crm\src\components\BalanceManagement\_entities\Balance;
use crm\src\components\BalanceManagement\_common\adapters\BalanceResult;
use crm\src\components\BalanceManagement\_common\interfaces\IBalanceResult;
use crm\src\components\BalanceManagement\_common\interfaces\IBalanceRepository;
use crm\src\components\BalanceManagement\_exceptions\BalanceManagementException;

class CreateBalance
{
    public function __construct(
        private IBalanceRepository $balanceRepository,
        private IValidation $validator,
    ) {
    }

    /**
     * Создаёт новую запись баланса на основе объекта Balance.
     *
     * Проводит валидацию данных, сохраняет баланс в репозиторий
     * и возвращает результат операции с объектом Balance или ошибкой.
     *
     * @param  Balance $balance Объект Balance с необходимыми данными (leadId обязателен).
     * @return IBalanceResult Результат операции: успешный с Balance или неуспешный с ошибкой.
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
            $balanceId = $this->balanceRepository->save($balance);
            if ($balanceId === null || $balanceId <= 0) {
                return BalanceResult::failure(
                    new BalanceManagementException('Не удалось сохранить баланс')
                );
            }

            $balance->id = $balanceId;

            return BalanceResult::success($balance);
        } catch (Throwable $e) {
            return BalanceResult::failure($e);
        }
    }
}
