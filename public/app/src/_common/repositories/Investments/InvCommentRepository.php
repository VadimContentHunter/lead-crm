<?php

namespace crm\src\_common\repositories\Investments;

use crm\src\_common\interfaces\AResultRepository;
use crm\src\Investments\InvComment\_mappers\InvCommentMapper;
use crm\src\Investments\InvComment\_common\DTOs\DbInvCommentDto;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;
use crm\src\Investments\InvComment\_common\InvCommentCollection;
use crm\src\Investments\InvComment\_common\adapters\InvCommentResult;
use crm\src\Investments\InvComment\_common\interfaces\IInvCommentResult;
use crm\src\Investments\InvComment\_common\interfaces\IInvCommentRepository;

/**
 * Репозиторий для комментариев к инвестициям.
 *
 * @extends AResultRepository<DbInvCommentDto>
 */
class InvCommentRepository extends AResultRepository implements IInvCommentRepository
{
    protected function getTableName(): string
    {
        return 'inv_InvComments';
    }

    protected function getEntityClass(): string
    {
        return DbInvCommentDto::class;
    }

    protected function fromArray(): callable
    {
        return fn(array $data): DbInvCommentDto => InvCommentMapper::fromArrayToDb($data);
    }

    protected function toArray(object $entity): array
    {
        /**
 * @var DbInvCommentDto $entity
*/
        return InvCommentMapper::fromDbToArray($entity);
    }

    protected function getResultClass(): string
    {
        return InvCommentResult::class;
    }

    public function getAllByLeadUid(string $leadUid): IInvCommentResult
    {
        try {
            $dtoList = $this->getAllByColumnValues('lead_uid', [$leadUid])->getArray();

            $entities = array_map(
                fn(DbInvCommentDto $dto) => InvCommentMapper::fromDbToEntity($dto),
                $dtoList
            );

            return InvCommentResult::success(new InvCommentCollection($entities));
        } catch (\Throwable $e) {
            return InvCommentResult::failure($e);
        }
    }

    public function deleteAllByLeadUid(string $leadUid): IInvCommentResult
    {
        try {
            $collection = $this->getAllByLeadUid($leadUid)->getCollection();
            $entities = $collection?->getAll() ?? [];
            $ids = array_filter(array_map(fn($entity) => $entity->id, $entities));

            if (empty($ids)) {
                return InvCommentResult::success([]);
            }

            $placeholders = implode(', ', array_map(fn($i) => ":id_$i", array_keys($ids)));
            $params = [];
            foreach ($ids as $index => $id) {
                $params["id_$index"] = $id;
            }

            $sql = sprintf("DELETE FROM %s WHERE id IN (%s)", $this->getTableName(), $placeholders);
            $this->repository->executeSql($sql, $params);

            return InvCommentResult::success($ids);
        } catch (\Throwable $e) {
            return InvCommentResult::failure($e);
        }
    }

    public function getAllByWhoId(string $whoId): IInvCommentResult
    {
        try {
            $dtoList = $this->getAllByColumnValues('who_id', [$whoId])->getArray();

            $entities = array_map(
                fn(DbInvCommentDto $dto) => InvCommentMapper::fromDbToEntity($dto),
                $dtoList
            );

            return InvCommentResult::success(new InvCommentCollection($entities));
        } catch (\Throwable $e) {
            return InvCommentResult::failure($e);
        }
    }

    public function getByLeadUidAndOption(string $leadUid, int $option): IInvCommentResult
    {
        try {
            $dtoList = $this->repository->executeQuery(
                (new QueryBuilder())
                    ->table($this->getTableName())
                    ->where('lead_uid = :lead_uid AND option = :option')
                    ->bindings(['lead_uid' => $leadUid, 'option' => $option])
                    ->select()
            )->getValidMappedList($this->fromArray());

            $entities = array_map(
                fn(DbInvCommentDto $dto) => InvCommentMapper::fromDbToEntity($dto),
                $dtoList
            );

            return InvCommentResult::success(new InvCommentCollection($entities));
        } catch (\Throwable $e) {
            return InvCommentResult::failure($e);
        }
    }
}
