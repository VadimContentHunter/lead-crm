<?php

namespace crm\src\_common\repositories\Investments;

use crm\src\_common\repositories\AResultRepository;
use crm\src\Investments\InvSource\_mappers\InvSourceMapper;
use crm\src\Investments\InvSource\_common\DTOs\DbInvSourceDto;
use crm\src\Investments\InvSource\_common\adapters\InvSourceResult;
use crm\src\Investments\InvSource\_common\interfaces\IInvSourceRepository;
use crm\src\Investments\InvSource\_common\interfaces\IInvSourceResult;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;

/**
 * Репозиторий для инвестиционных источников.
 *
 * @extends AResultRepository<DbInvSourceDto>
 */
class InvSourceRepository extends AResultRepository implements IInvSourceRepository
{
    protected function getTableName(): string
    {
        return 'inv_sources';
    }

    protected function getEntityClass(): string
    {
        return DbInvSourceDto::class;
    }

    protected function fromArray(): callable
    {
        return fn(array $data): DbInvSourceDto => InvSourceMapper::fromArrayToDb($data);
    }

    protected function toArray(object $entity): array
    {
        /**
 * @var DbInvSourceDto $entity
*/
        return InvSourceMapper::fromDbToArray($entity);
    }

    protected function getResultClass(): string
    {
        return InvSourceResult::class;
    }

    public function getByCode(string $code): IInvSourceResult
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
                throw new \RuntimeException("Источник с кодом '$code' не найден.");
            }

            return InvSourceResult::success(InvSourceMapper::fromDbToEntity($dto));
        } catch (\Throwable $e) {
            return InvSourceResult::failure($e);
        }
    }

    public function deleteByCode(string $code): IInvSourceResult
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
                throw new \RuntimeException("Невозможно удалить: источник с кодом '$code' не найден.");
            }

            $this->deleteById($dto->id);
            return InvSourceResult::success($dto->id);
        } catch (\Throwable $e) {
            return InvSourceResult::failure($e);
        }
    }
}
