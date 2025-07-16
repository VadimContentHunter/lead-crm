<?php

namespace crm\src\_common\repositories\Investments;

use Domain\Investment\DTOs\DbInvStatusDto;
use crm\src\_common\repositories\AResultRepository;
use crm\src\Investments\InvStatus\_mappers\InvStatusMapper;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;
use crm\src\Investments\InvStatus\_common\adapters\InvStatusResult;
use crm\src\Investments\InvStatus\_common\interfaces\IInvStatusResult;
use crm\src\Investments\InvStatus\_common\interfaces\IInvStatusRepository;

/**
 * Репозиторий для инвестиционных статусов.
 *
 * @extends AResultRepository<DbInvStatusDto>
 */
class InvStatusRepository extends AResultRepository implements IInvStatusRepository
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
        return fn(array $data): DbInvStatusDto => InvStatusMapper::fromArrayToDb($data);
    }

    protected function toArray(object $entity): array
    {
        /**
         * @var DbInvStatusDto $entity
         */
        return InvStatusMapper::fromDbToArray($entity);
    }

    protected function getResultClass(): string
    {
        return InvStatusResult::class;
    }

    public function getByCode(string $code): IInvStatusResult
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

            return InvStatusResult::success(InvStatusMapper::fromDbToEntity($dto));
        } catch (\Throwable $e) {
            return InvStatusResult::failure($e);
        }
    }

    public function deleteByCode(string $code): IInvStatusResult
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
            return InvStatusResult::success($dto->id);
        } catch (\Throwable $e) {
            return InvStatusResult::failure($e);
        }
    }
}
