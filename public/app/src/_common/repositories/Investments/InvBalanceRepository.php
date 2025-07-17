<?php

namespace crm\src\_common\repositories\Investments;

use crm\src\_common\interfaces\AResultRepository;
use crm\src\Investments\InvBalance\_common\DTOs\DbInvBalanceDto;
use crm\src\Investments\InvBalance\_exceptions\InvBalanceException;
use crm\src\Investments\InvBalance\_common\mappers\InvBalanceMapper;
use crm\src\Investments\InvBalance\_common\adapters\InvBalanceResult;
use crm\src\Investments\InvBalance\_common\interfaces\IInvBalanceResult;
use crm\src\Investments\InvBalance\_common\interfaces\IInvBalanceRepository;

/**
 * Репозиторий для инвестиционного баланса.
 *
 * @extends AResultRepository<DbInvBalanceDto>
 */
class InvBalanceRepository extends AResultRepository implements IInvBalanceRepository
{
    protected function getTableName(): string
    {
        return 'inv_balances';
    }

    /**
     * @return class-string<DbInvBalanceDto>
     */
    protected function getEntityClass(): string
    {
        return DbInvBalanceDto::class;
    }

    /**
     * @return callable(array<string, mixed>): DbInvBalanceDto
     */
    protected function fromArray(): callable
    {
        return fn(array $data): DbInvBalanceDto => InvBalanceMapper::fromArrayToDb($data);
    }

    /**
     * @param  object $entity
     * @return array<string, mixed>
     */
    protected function toArray(object $entity): array
    {
        /**
 * @var DbInvBalanceDto $entity
*/
        return InvBalanceMapper::fromDbToArray($entity);
    }

    /**
     * @return class-string<IInvBalanceResult>
     */
    protected function getResultClass(): string
    {
        return InvBalanceResult::class;
    }

    /**
     * Получить баланс по lead_uid.
     *
     * @param  string $leadUid
     * @return IInvBalanceResult
     */
    public function getByLeadUid(string $leadUid): IInvBalanceResult
    {
        try {
            $dto = $this->getAllByColumnValues('lead_uid', [$leadUid]);

            if (!$dto->isSuccess() || $dto->isEmpty()) {
                return InvBalanceResult::failure($dto->getError() ?? new InvBalanceException("Баланс не найден"));
            }

            $entity = InvBalanceMapper::fromDbToEntity($dto->first()->getData());
            return InvBalanceResult::success($entity);
        } catch (\Throwable $e) {
            return InvBalanceResult::failure($e);
        }
    }

    /**
     * Удалить баланс по lead_uid.
     *
     * @param  string $leadUid
     * @return IInvBalanceResult
     */
    public function deleteByLeadUid(string $leadUid): IInvBalanceResult
    {
        try {
            $sql = sprintf('DELETE FROM %s WHERE lead_uid = :lead_uid', $this->getTableName());
            $this->repository->executeSql($sql, ['lead_uid' => $leadUid]);
            return InvBalanceResult::success(true);
        } catch (\Throwable $e) {
            return InvBalanceResult::failure($e);
        }
    }
}
