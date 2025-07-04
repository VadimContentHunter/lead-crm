<?php

namespace crm\src\components\BalanceManagement;

use Throwable;
use crm\src\components\BalanceManagement\_common\adapters\BalanceResult;
use crm\src\components\LeadManagement\_common\interfaces\ILeadRepository;
use crm\src\components\BalanceManagement\_common\interfaces\IBalanceResult;
use crm\src\components\BalanceManagement\_common\interfaces\IBalanceRepository;
use crm\src\components\BalanceManagement\_exceptions\BalanceManagementException;

class GetBalance
{
    public function __construct(
        private IBalanceRepository $repository,
        private ILeadRepository $leadRepository
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
     * Получает баланс по leadId с проверкой существования лида.
     *
     * @param  int $leadId ID лида.
     * @return IBalanceResult Результат операции с найденным балансом или ошибкой.
     */
    public function getByLeadId(int $leadId): IBalanceResult
    {
        try {
            $lead = $this->leadRepository->getById($leadId);
            if ($lead === null) {
                return BalanceResult::failure(new BalanceManagementException("Лид не найден по идентификатору: $leadId"));
            }

            $balance = $this->repository->getByLeadId($leadId);
            if ($balance === null || !$balance) {
                return BalanceResult::failure(new BalanceManagementException("Баланс не найден по leadId: $leadId"));
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

    /**
     * Возвращает названия столбцов таблицы пользователей.
     *
     * @param  array<string, string> $renameMap Ключ — оригинальное имя, значение — новое имя
     * @return IUserResult
     */
    public function executeColumnNames(array $renameMap = []): IBalanceResult
    {
        try {
            $columns = $this->repository->getColumnNames();

            if (!empty($renameMap)) {
                $columns = array_map(
                    fn($name) => $renameMap[$name] ?? $name,
                    $columns
                );
            }

            return BalanceResult::success($columns);
        } catch (\Throwable $e) {
            return BalanceResult::failure($e);
        }
    }
}
