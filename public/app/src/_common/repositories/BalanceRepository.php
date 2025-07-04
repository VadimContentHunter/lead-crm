<?php

namespace crm\src\_common\repositories;

use crm\src\_common\interfaces\ARepository;
use crm\src\components\BalanceManagement\_entities\Balance;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;
use crm\src\components\BalanceManagement\_common\mappers\BalanceMapper;
use crm\src\components\BalanceManagement\_common\interfaces\IBalanceRepository;

/**
 * @extends ARepository<Balance>
 */
class BalanceRepository extends ARepository implements IBalanceRepository
{
    protected function getTableName(): string
    {
        return 'balances';
    }

    protected function getEntityClass(): string
    {
        return Balance::class;
    }

    protected function fromArray(): callable
    {
        return [BalanceMapper::class, 'fromArray'];
    }

    protected function toArray(object $entity): array
    {
        /**
         * @var Balance $entity
         */
        return BalanceMapper::toArray($entity);
    }

    public function deleteByLeadId(int $leadId): ?int
    {
        return $this->repository->executeQuery(
            (new QueryBuilder())
                ->table($this->getTableName())
                ->where('lead_id = :lead_id')
                ->delete(['lead_id' => $leadId])
        )->getInt();
    }

    public function getByLeadId(int $leadId): ?Balance
    {
        return $this->repository->executeQuery(
            (new QueryBuilder())
                ->table($this->getTableName())
                ->where('lead_id = :lead_id')
                ->limit(1)
                ->select(['lead_id' => $leadId])
        )->first()->getObjectOrNullWithMapper($this->getEntityClass(), [BalanceMapper::class, 'fromArray']);
    }

    public function updateByLeadId(Balance $balance): bool
    {
        $data = BalanceMapper::toArray($balance);
        unset($data['id']);
        return $this->repository->executeQuery(
            (new QueryBuilder())
                ->table($this->getTableName())
                ->where('lead_id = :lead_id')
                ->update($data)
        )->getBool();
    }
}
