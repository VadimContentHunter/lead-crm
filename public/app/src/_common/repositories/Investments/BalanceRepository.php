<?php

namespace crm\src\_common\repositories\Investments;

use crm\src\_common\repositories\AResultRepository;
use crm\src\Investments\Balance\_mappers\BalanceMapper;
use crm\src\Investments\Balance\_common\DTOs\DbInvBalanceDto;
use crm\src\Investments\Balance\_common\interfaces\IBalanceRepository;
use crm\src\Investments\Balance\_common\interfaces\IBalanceResult;
use crm\src\Investments\Balance\_common\adapters\BalanceResult;

/**
 * Репозиторий для инвестиционного баланса.
 *
 * @extends AResultRepository<DbInvBalanceDto>
 */
class BalanceRepository extends AResultRepository implements IBalanceRepository
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
        return fn(array $data): DbInvBalanceDto => BalanceMapper::fromArrayToDb($data);
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
        return BalanceMapper::fromDbToArray($entity);
    }

    /**
     * @return class-string<IBalanceResult>
     */
    protected function getResultClass(): string
    {
        return BalanceResult::class;
    }

    /**
     * Получить баланс по lead_uid.
     *
     * @param  string $leadUid
     * @return IBalanceResult
     */
    public function getByLeadUid(string $leadUid): IBalanceResult
    {
        try {
            $dto = $this->getAllByColumnValues('lead_uid', [$leadUid])
                        ->first()
                        ->getData(); // DbInvBalanceDto

            $entity = BalanceMapper::fromDbToEntity($dto);
            return BalanceResult::success($entity);
        } catch (\Throwable $e) {
            return BalanceResult::failure($e);
        }
    }

    /**
     * Удалить баланс по lead_uid.
     *
     * @param  string $leadUid
     * @return IBalanceResult
     */
    public function deleteByLeadUid(string $leadUid): IBalanceResult
    {
        try {
            $sql = sprintf('DELETE FROM %s WHERE lead_uid = :lead_uid', $this->getTableName());
            $this->repository->executeSql($sql, ['lead_uid' => $leadUid]);
            return BalanceResult::success(true);
        } catch (\Throwable $e) {
            return BalanceResult::failure($e);
        }
    }
}
