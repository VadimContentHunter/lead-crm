<?php

namespace crm\src\_common\repositories\Investments;

use crm\src\_common\interfaces\AResultRepository;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;
use crm\src\Investments\InvActivity\_mappers\InvActivityMapper;
use crm\src\Investments\InvActivity\_common\DTOs\DbInvActivityDto;
use crm\src\Investments\InvActivity\_common\InvActivityCollection;
use crm\src\Investments\InvActivity\_common\adapters\InvActivityResult;
use crm\src\Investments\InvActivity\_common\interfaces\IInvActivityResult;
use crm\src\Investments\InvActivity\_common\interfaces\IInvActivityRepository;

/**
 * Репозиторий для инвестиционных сделок (InvActivity).
 *
 * @extends AResultRepository<DbInvActivityDto>
 */
class InvActivityRepository extends AResultRepository implements IInvActivityRepository
{
    protected function getTableName(): string
    {
        return 'inv_activities';
    }

    protected function getEntityClass(): string
    {
        return DbInvActivityDto::class;
    }

    protected function fromArray(): callable
    {
        return fn(array $data): DbInvActivityDto => InvActivityMapper::fromArrayToDb($data);
    }

    protected function toArray(object $entity): array
    {
        /**
 * @var DbInvActivityDto $entity
*/
        return InvActivityMapper::fromDbToArray($entity);
    }

    protected function getResultClass(): string
    {
        return InvActivityResult::class;
    }

    public function getAllByLeadUid(string $leadUid): IInvActivityResult
    {
        try {
            $dtoList = $this->getAllByColumnValues('lead_uid', [$leadUid])->getArray();

            $entities = array_map(
                fn(DbInvActivityDto $dto) => InvActivityMapper::fromDbToEntity($dto),
                $dtoList
            );

            return InvActivityResult::success(new InvActivityCollection($entities));
        } catch (\Throwable $e) {
            return InvActivityResult::failure($e);
        }
    }

    public function getAllActiveByLeadUid(string $leadUid): IInvActivityResult
    {
        try {
            $dtoList = $this->repository->executeQuery(
                (new QueryBuilder())
                    ->table($this->getTableName())
                    ->where('lead_uid = :lead_uid AND type = :type')
                    ->bindings(['lead_uid' => $leadUid, 'type' => 'active'])
                    ->select()
            )->getValidMappedList($this->fromArray());

            $entities = array_map(
                fn(DbInvActivityDto $dto) => InvActivityMapper::fromDbToEntity($dto),
                $dtoList
            );

            return InvActivityResult::success(new InvActivityCollection($entities));
        } catch (\Throwable $e) {
            return InvActivityResult::failure($e);
        }
    }

    public function getAllClosedByLeadUid(string $leadUid): IInvActivityResult
    {
        try {
            $dtoList = $this->repository->executeQuery(
                (new QueryBuilder())
                    ->table($this->getTableName())
                    ->where('lead_uid = :lead_uid AND type = :type')
                    ->bindings(['lead_uid' => $leadUid, 'type' => 'closed'])
                    ->select()
            )->getValidMappedList($this->fromArray());

            $entities = array_map(
                fn(DbInvActivityDto $dto) => InvActivityMapper::fromDbToEntity($dto),
                $dtoList
            );

            return InvActivityResult::success(new InvActivityCollection($entities));
        } catch (\Throwable $e) {
            return InvActivityResult::failure($e);
        }
    }

    public function deleteAllByLeadUid(string $leadUid): IInvActivityResult
    {
        try {
            $collection = $this->getAllByLeadUid($leadUid)->getCollection();
            $entities = $collection?->getAll() ?? [];
            $ids = array_filter(array_map(fn($entity) => $entity->id, $entities));

            if (empty($ids)) {
                return InvActivityResult::success([]);
            }

            $placeholders = implode(', ', array_map(fn($i) => ":id_$i", array_keys($ids)));
            $params = [];
            foreach ($ids as $index => $id) {
                $params["id_$index"] = $id;
            }

            $sql = sprintf("DELETE FROM %s WHERE id IN (%s)", $this->getTableName(), $placeholders);
            $this->repository->executeSql($sql, $params);

            return InvActivityResult::success($ids);
        } catch (\Throwable $e) {
            return InvActivityResult::failure($e);
        }
    }
}
