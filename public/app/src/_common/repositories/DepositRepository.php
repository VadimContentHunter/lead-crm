<?php

namespace crm\src\_common\repositories;

use crm\src\_common\interfaces\ARepository;
use crm\src\components\DepositManagement\_entities\Deposit;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;
use crm\src\components\DepositManagement\_common\mappers\DepositMapper;
use crm\src\components\DepositManagement\_common\interfaces\IDepositRepository;

/**
 * @extends ARepository<Deposit>
 */
class DepositRepository extends ARepository implements IDepositRepository
{
    protected function getTableName(): string
    {
        return 'deposits';
    }

    /**
     * @return class-string<Deposit>
     */
    protected function getEntityClass(): string
    {
        return Deposit::class;
    }

    protected function fromArray(): callable
    {
        return [DepositMapper::class, 'fromArray'];
    }

    protected function toArray(object $entity): array
    {
        /**
         * @var Deposit $entity
         */
        return DepositMapper::toArray($entity);
    }

    /**
     * @param object|array<string,mixed> $entity
     */
    public function save(object|array $entity): ?int
    {
        $data = is_array($entity) ? $entity : $this->toArray($entity);
        if ($data['created_at'] === null) {
            unset($data['created_at']);
        }
        return $this->repository->executeQuery(
            (new QueryBuilder())->table($this->getTableName())->insert($data)
        )->getInt();
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

    public function getByLeadId(int $leadId): ?Deposit
    {
        /**
         * @var callable(array<string, mixed>): Deposit $mapper
         */
        $mapper = [DepositMapper::class, 'fromArray'];
        return $this->repository->executeQuery(
            (new QueryBuilder())
                ->table($this->getTableName())
                ->where('lead_id = :lead_id')
                ->limit(1)
                ->select(['lead_id' => $leadId])
        )->first()->getObjectOrNullWithMapper($this->getEntityClass(), $mapper);
    }

    public function updateByLeadId(Deposit $deposit): bool
    {
        $data = DepositMapper::toArray($deposit);
        unset($data['id'], $data['created_at']);

        return $this->repository->executeQuery(
            (new QueryBuilder())
                ->table($this->getTableName())
                ->where('lead_id = :lead_id')
                ->update($data)
        )->getBool() ?? false;
    }
}
