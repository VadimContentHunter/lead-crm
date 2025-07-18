<?php

namespace crm\src\_common\repositories\Investments;

use crm\src\_common\interfaces\AResultRepository;
use crm\src\Investments\InvLead\_common\DTOs\DbInvLeadDto;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;
use crm\src\Investments\InvLead\_common\mappers\InvLeadMapper;
use crm\src\Investments\InvLead\_common\adapters\InvLeadResult;
use crm\src\Investments\InvLead\_common\interfaces\IInvLeadResult;
use crm\src\Investments\InvLead\_common\interfaces\IInvLeadRepository;

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
                fn(array $data) => empty($data)
                    ? throw new \RuntimeException("Лид с UID '$uid' не найден.")
                    : ($this->fromArray())($data)
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
                fn(array $data) => empty($data)
                    ? throw new \RuntimeException("Лид с UID '$uid' не найден.")
                    : ($this->fromArray())($data)
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
            $data = [];

            if (is_object($entityOrData)) {
                if (!$entityOrData instanceof DbInvLeadDto) {
                    return InvLeadResult::failure(new \InvalidArgumentException(
                        "Ожидался объект типа " . DbInvLeadDto::class . ", передан " . get_class($entityOrData)
                    ));
                }

                $data = $this->toArray($entityOrData);
            } elseif (is_array($entityOrData)) {
                $data = $entityOrData;
            }

            if (!isset($data['uid']) || !is_string($data['uid']) || trim($data['uid']) === '') {
                return InvLeadResult::failure(new \InvalidArgumentException("Поле 'uid' обязательно для update()"));
            }

            $bindings = $data;
            unset($data['uid']);

            if (empty($data)) {
                return InvLeadResult::failure(new \RuntimeException("Нет данных для обновления"));
            }

            $result = $this->repository->executeQuery(
                (new QueryBuilder())
                ->table($this->getTableName())
                ->where('uid = :uid')
                ->bindings($bindings)
                ->update($data)
            );

            if (!$result->isSuccess()) {
                return InvLeadResult::failure($result->getError() ?? new \RuntimeException("Не удалось обновить"));
            }

            return InvLeadResult::success($result->getInt());
        } catch (\Throwable $e) {
            return InvLeadResult::failure($e);
        }
    }

    public function getAllByAccountManagerId(int $managerId): IInvLeadResult
    {
        try {
            $list = $this->repository->executeQuery(
                (new QueryBuilder())
                ->table($this->getTableName())
                ->where('account_manager_id = :managerId')
                ->bindings(['managerId' => $managerId])
                ->select()
            )->getValidMappedList($this->fromArray());

            return InvLeadResult::success($list);
        } catch (\Throwable $e) {
            return InvLeadResult::failure($e);
        }
    }
}
