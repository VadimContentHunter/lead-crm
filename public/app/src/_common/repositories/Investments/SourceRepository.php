<?php

namespace crm\src\_common\repositories\Investments;

use crm\src\_common\repositories\AResultRepository;
use crm\src\Investments\Source\_mappers\SourceMapper;
use crm\src\Investments\Source\_common\DTOs\DbInvSourceDto;
use crm\src\Investments\Source\_common\adapters\SourceResult;
use crm\src\Investments\Source\_common\interfaces\ISourceRepository;
use crm\src\Investments\Source\_common\interfaces\ISourceResult;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;

/**
 * Репозиторий для инвестиционных источников.
 *
 * @extends AResultRepository<DbInvSourceDto>
 */
class SourceRepository extends AResultRepository implements ISourceRepository
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
        return fn(array $data): DbInvSourceDto => SourceMapper::fromArrayToDb($data);
    }

    protected function toArray(object $entity): array
    {
        /**
 * @var DbInvSourceDto $entity
*/
        return SourceMapper::fromDbToArray($entity);
    }

    protected function getResultClass(): string
    {
        return SourceResult::class;
    }

    public function getByCode(string $code): ISourceResult
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

            return SourceResult::success(SourceMapper::fromDbToEntity($dto));
        } catch (\Throwable $e) {
            return SourceResult::failure($e);
        }
    }

    public function deleteByCode(string $code): ISourceResult
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
            return SourceResult::success($dto->id);
        } catch (\Throwable $e) {
            return SourceResult::failure($e);
        }
    }
}
