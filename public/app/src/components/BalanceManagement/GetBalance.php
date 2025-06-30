<?php

namespace crm\src\components\BalanceManagement\_usecases;

use Throwable;
use crm\src\components\BalanceManagement\_common\adapters\BalanceResult;
use crm\src\components\BalanceManagement\_common\interfaces\IBalanceResult;
use crm\src\components\BalanceManagement\_common\interfaces\IBalanceRepository;

class GetBalance
{
    public function __construct(
        private IBalanceRepository $repository,
    ) {
    }

    /**
     * Получает баланс по ID.
     *
     * @param  int $id ID баланса.
     * @return IBalanceResult Результат операции с найденным балансом или ошибкой.
     */
    public function getById(int $id): IBalanceResult
    {
        try {
            $balance = $this->repository->getById($id);
            if ($balance === null) {
                return BalanceResult::failure(new \Exception("Balance not found by id: $id"));
            }
            return BalanceResult::success($balance);
        } catch (Throwable $e) {
            return BalanceResult::failure($e);
        }
    }

    /**
     * Получает баланс по leadId.
     *
     * @param  int $leadId ID лида.
     * @return IBalanceResult Результат операции с найденным балансом или ошибкой.
     */
    public function getByLeadId(int $leadId): IBalanceResult
    {
        try {
            $balance = $this->repository->getByLeadId($leadId);
            if ($balance === null) {
                return BalanceResult::failure(new \Exception("Balance not found by leadId: $leadId"));
            }
            return BalanceResult::success($balance);
        } catch (Throwable $e) {
            return BalanceResult::failure($e);
        }
    }

    /**
     * Получает все балансы.
     *
     * @return IBalanceResult Результат операции с массивом балансов или ошибкой.
     */
    public function getAll(): IBalanceResult
    {
        try {
            $balances = $this->repository->getAll();
            return BalanceResult::success($balances);
        } catch (Throwable $e) {
            return BalanceResult::failure($e);
        }
    }
}
