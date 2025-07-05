<?php

namespace crm\src\components\LeadManagement;

use Throwable;
use crm\src\components\LeadManagement\_common\interfaces\ILeadRepository;
use crm\src\components\LeadManagement\_common\adapters\LeadResult;
use crm\src\components\LeadManagement\_common\interfaces\ILeadResult;
use crm\src\components\LeadManagement\_exceptions\LeadManagementException;

class DeleteLead
{
    public function __construct(
        private ILeadRepository $repository,
    ) {
    }

    /**
     * Удаляет всех лидов с заданным accountManagerId.
     *
     * @param  int $accountManagerId
     * @return ILeadResult Возвращает количество удалённых лидов
     */
    public function execute(int $accountManagerId): ILeadResult
    {
        try {
            $deletedCount = $this->repository->deleteByAccountManagerId($accountManagerId);

            if ($deletedCount === null || $deletedCount < 0) {
                return LeadResult::failure(
                    new LeadManagementException("Лиды с accountManagerId=$accountManagerId не найдены или не удалены")
                );
            }

            return LeadResult::success($deletedCount);
        } catch (Throwable $e) {
            return LeadResult::failure($e);
        }
    }

    /**
     * Удаляет пользователя по ID.
     *
     * @param  int $id ID пользователя.
     * @return ILeadResult Результат операции: успешный с ID удалённого пользователя или с ошибкой.
     */
    public function byId(int $id): ILeadResult
    {
        try {
            $deletedId = $this->repository->deleteById($id);

            if ($deletedId < 0) {
                return LeadResult::failure(
                    new LeadManagementException("LEAD с ID {$id} не найден или не удалён")
                );
            }

            return LeadResult::success($deletedId);
        } catch (\Throwable $e) {
            return LeadResult::failure($e);
        }
    }
}
