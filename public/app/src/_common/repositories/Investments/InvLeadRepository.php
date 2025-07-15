<?php

namespace crm\src\_common\repositories\Investments;

use crm\src\_common\repositories\AResultRepository;
use crm\src\Investments\Lead\_mappers\InvLeadMapper;
use crm\src\Investments\Lead\_dto\DbInvLeadDto;
use crm\src\Investments\Lead\_common\adapters\InvLeadResult;
use crm\src\Investments\Lead\_common\interfaces\IInvLeadRepository;
use crm\src\Investments\Lead\_common\interfaces\IInvLeadResult;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;

/**
 * Репозиторий для инвестиционных лидов.
 *
 * @extends AResultRepository<DbInvLeadDto>
 */
class InvLeadRepository extends AResultRepository implements IInvLeadRepository
{
    protected function getTableName(): string
    {
        return 'inv_leads';
    }

    protected function getEntityClass(): string
    {
        return DbInvLeadDto::class;
    }

    protected function fromArray(): callable
    {
        return fn(array $data): DbInvLeadDto => InvLeadMapper::fromArrayToDb($data);
    }

    protected function toArray(object $entity): array
    {
        /**
 * @var DbInvLeadDto $entity
*/
        return InvLeadMapper::fromDbToArray($entity);
    }

    protected function getResultClass(): string
    {
        return InvLeadResult::class;
    }

    public function getByUid(string $uid): IInvLeadResult
    {
        try {
            $dto = $this->repository->executeQuery(
                (new QueryBuilder())
                    ->table($this->getTableName())
                    ->where('uid = :uid')
                    ->limit(1)
                    ->bindings(['uid' => $uid])
                    ->select()
            )->first()->getObjectOrNullWithMapper(
                $this->getEntityClass(),
                $this->fromArray()
            );

            if (!$dto) {
                throw new \RuntimeException("Лид с UID '$uid' не найден.");
            }

            return InvLeadResult::success(InvLeadMapper::fromDbToEntity($dto));
        } catch (\Throwable $e) {
            return InvLeadResult::failure($e);
        }
    }

    public function deleteByUid(string $uid): IInvLeadResult
    {
        try {
            $dto = $this->repository->executeQuery(
                (new QueryBuilder())
                ->table($this->getTableName())
                ->where('uid = :uid')
                ->limit(1)
                ->bindings(['uid' => $uid])
                ->select()
            )->first()->getObjectOrNullWithMapper(
                $this->getEntityClass(),
                $this->fromArray()
            );

            if (!$dto || !$dto->uid) {
                throw new \RuntimeException("Невозможно удалить: лид с UID '$uid' не найден.");
            }

            $this->repository->executeQuery(
                (new QueryBuilder())
                ->table($this->getTableName())
                ->where('uid = :uid')
                ->bindings(['uid' => $uid])
                ->delete()
            );

            return InvLeadResult::success($dto->uid);
        } catch (\Throwable $e) {
            return InvLeadResult::failure($e);
        }
    }

    public function getById(int $id): IInvLeadResult
    {
        // Преобразуем int → string, потому что UID — строка
        return $this->getByUid((string) $id);
    }

    public function deleteById(int $id): IInvLeadResult
    {
        // Преобразуем int → string, потому что UID — строка
        return $this->deleteByUid((string) $id);
    }

    public function update(object|array $entityOrData): IInvLeadResult
    {
        try {
            if (is_object($entityOrData)) {
                $class = $this->getEntityClass();

                if (!$entityOrData instanceof $class) {
                    return $this->wrapFailure(new \InvalidArgumentException(
                        "Ожидался объект типа {$class}, передан " . get_class($entityOrData)
                    ));
                }

                /**
                 * @var TEntity $entityOrData
                */
                $data = $this->toArray($entityOrData);
            } else {
                $data = $entityOrData;
            }

            if (!isset($data['uid'])) {
                return $this->wrapFailure(new \InvalidArgumentException("Поле 'uid' обязательно для update()"));
            }

            $uid = $data['uid'];
            unset($data['uid']);

            if (empty($data)) {
                return $this->wrapFailure(new \RuntimeException("Нет данных для обновления"));
            }

            $result = $this->repository->executeQuery(
                (new QueryBuilder())
                ->table($this->getTableName())
                ->where('uid = :uid')
                ->bindings(['uid' => $uid])
                ->update($data)
            )->getInt();

            return $this->wrapSuccess($result);
        } catch (\Throwable $e) {
            return $this->wrapFailure($e);
        }
    }
}
