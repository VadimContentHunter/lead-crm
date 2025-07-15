<?php

namespace crm\src\_common\repositories\Investments;

use crm\src\_common\repositories\AResultRepository;
use crm\src\Investments\Activity\_mappers\ActivityMapper;
use crm\src\Investments\Activity\_common\DTOs\DbActivityDto;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;
use crm\src\Investments\Activity\_common\InvActivityCollection;
use crm\src\Investments\Activity\_common\adapters\ActivityResult;
use crm\src\Investments\Activity\_common\interfaces\IActivityResult;
use crm\src\Investments\Activity\_common\interfaces\IActivityRepository;

/**
 * Репозиторий для инвестиционных сделок (activity).
 *
 * @extends AResultRepository<DbActivityDto>
 */
class ActivityRepository extends AResultRepository implements IActivityRepository
{
    protected function getTableName(): string
    {
        return 'inv_activities';
    }

    protected function getEntityClass(): string
    {
        return DbActivityDto::class;
    }

    protected function fromArray(): callable
    {
        return fn(array $data): DbActivityDto => ActivityMapper::fromArrayToDb($data);
    }

    protected function toArray(object $entity): array
    {
        /**
 * @var DbActivityDto $entity
*/
        return ActivityMapper::fromDbToArray($entity);
    }

    protected function getResultClass(): string
    {
        return ActivityResult::class;
    }

    public function getAllByLeadUid(string $leadUid): IActivityResult
    {
        try {
            $dtoList = $this->getAllByColumnValues('lead_uid', [$leadUid])->getArray();

            $entities = array_map(
                fn(DbActivityDto $dto) => ActivityMapper::fromDbToEntity($dto),
                $dtoList
            );

            return ActivityResult::success(new InvActivityCollection($entities));
        } catch (\Throwable $e) {
            return ActivityResult::failure($e);
        }
    }

    public function getAllActiveByLeadUid(string $leadUid): IActivityResult
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
                fn(DbActivityDto $dto) => ActivityMapper::fromDbToEntity($dto),
                $dtoList
            );

            return ActivityResult::success(new InvActivityCollection($entities));
        } catch (\Throwable $e) {
            return ActivityResult::failure($e);
        }
    }

    public function getAllClosedByLeadUid(string $leadUid): IActivityResult
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
                fn(DbActivityDto $dto) => ActivityMapper::fromDbToEntity($dto),
                $dtoList
            );

            return ActivityResult::success(new InvActivityCollection($entities));
        } catch (\Throwable $e) {
            return ActivityResult::failure($e);
        }
    }

    public function deleteAllByLeadUid(string $leadUid): IActivityResult
    {
        try {
            $collection = $this->getAllByLeadUid($leadUid)->getCollection();
            $entities = $collection?->getAll() ?? [];
            $ids = array_filter(array_map(fn($entity) => $entity->id, $entities));

            if (empty($ids)) {
                return ActivityResult::success([]);
            }

            $placeholders = implode(', ', array_map(fn($i) => ":id_$i", array_keys($ids)));
            $params = [];
            foreach ($ids as $index => $id) {
                $params["id_$index"] = $id;
            }

            $sql = sprintf("DELETE FROM %s WHERE id IN (%s)", $this->getTableName(), $placeholders);
            $this->repository->executeSql($sql, $params);

            return ActivityResult::success($ids);
        } catch (\Throwable $e) {
            return ActivityResult::failure($e);
        }
    }
}
