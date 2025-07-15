<?php

namespace crm\src\_common\repositories\Investments;

use Domain\Investment\DTOs\DbInvStatusDto;
use crm\src\_common\repositories\AResultRepository;
use crm\src\Investments\Status\_mappers\StatusMapper;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;
use crm\src\Investments\Status\_common\adapters\StatusResult;
use crm\src\Investments\Status\_common\interfaces\IStatusResult;
use crm\src\Investments\Status\_common\interfaces\IStatusRepository;

/**
 * Репозиторий для инвестиционных статусов.
 *
 * @extends AResultRepository<DbInvStatusDto>
 */
class StatusRepository extends AResultRepository implements IStatusRepository
{
    protected function getTableName(): string
    {
        return 'inv_statuses';
    }

    protected function getEntityClass(): string
    {
        return DbInvStatusDto::class;
    }

    protected function fromArray(): callable
    {
        return fn(array $data): DbInvStatusDto => StatusMapper::fromArrayToDb($data);
    }

    protected function toArray(object $entity): array
    {
        /**
         * @var DbInvStatusDto $entity
         */
        return StatusMapper::fromDbToArray($entity);
    }

    protected function getResultClass(): string
    {
        return StatusResult::class;
    }

    public function getByCode(string $code): IStatusResult
    {
        try {
            $dto = $this->repository->executeQuery(
                (new QueryBuilder())
                    ->table($this->getTableName())
                    ->where('code = :code')
                    ->limit(1)
                    ->bindings(['code' => $code])
                    ->select()
            )->first()->getObjectOrNullWithMapper(
                $this->getEntityClass(),
                $this->fromArray()
            );

            if (!$dto) {
                throw new \RuntimeException("Статус с кодом '$code' не найден.");
            }

            return StatusResult::success(StatusMapper::fromDbToEntity($dto));
        } catch (\Throwable $e) {
            return StatusResult::failure($e);
        }
    }

    public function deleteByCode(string $code): IStatusResult
    {
        try {
            $dto = $this->repository->executeQuery(
                (new QueryBuilder())
                    ->table($this->getTableName())
                    ->where('code = :code')
                    ->limit(1)
                    ->bindings(['code' => $code])
                    ->select()
            )->first()->getObjectOrNullWithMapper(
                $this->getEntityClass(),
                $this->fromArray()
            );

            if (!$dto || !$dto->id) {
                throw new \RuntimeException("Невозможно удалить: статус с кодом '$code' не найден.");
            }

            $this->deleteById($dto->id);
            return StatusResult::success($dto->id);
        } catch (\Throwable $e) {
            return StatusResult::failure($e);
        }
    }
}
