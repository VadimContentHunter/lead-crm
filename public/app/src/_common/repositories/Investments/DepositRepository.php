<?php

namespace crm\src\_common\repositories\Investments;

use crm\src\_common\repositories\AResultRepository;
use crm\src\Investments\Deposit\_common\DTOs\DbInvDepositDto;
use crm\src\Investments\Deposit\_common\interfaces\IDepositRepository;
use crm\src\Investments\Deposit\_common\interfaces\IDepositResult;
use crm\src\Investments\Deposit\_mappers\InvDepositMapper;
use crm\src\Investments\Deposit\_common\InvDepositCollection;
use crm\src\Investments\Deposit\_common\adapters\DepositResult;

/**
 * Репозиторий для депозитов.
 *
 * @extends AResultRepository<DbInvDepositDto>
 */
class DepositRepository extends AResultRepository implements IDepositRepository
{
    protected function getTableName(): string
    {
        return 'inv_deposits';
    }

    /**
     * @return class-string<DbInvDepositDto>
     */
    protected function getEntityClass(): string
    {
        return DbInvDepositDto::class;
    }

    /**
     * @return callable(array<string, mixed>): DbInvDepositDto
     */
    protected function fromArray(): callable
    {
        return fn(array $data): DbInvDepositDto => InvDepositMapper::fromArrayToDb($data);
    }

    protected function toArray(object $entity): array
    {
        /**
 * @var DbInvDepositDto $entity
*/
        return InvDepositMapper::fromDbToArray($entity);
    }

    /**
     * @return class-string<IDepositResult>
     */
    protected function getResultClass(): string
    {
        return DepositResult::class;
    }

    public function getAllByUid(string $uid): IDepositResult
    {
        try {
            $dtoList = $this->getAllByColumnValues('uid', [$uid])->getArray();

            $entities = array_map(
                fn(DbInvDepositDto $dto) => InvDepositMapper::fromDbToEntity($dto),
                $dtoList
            );

            return DepositResult::success(new InvDepositCollection($entities));
        } catch (\Throwable $e) {
            return DepositResult::failure($e);
        }
    }

    public function deleteAllByUid(string $uid): IDepositResult
    {
        try {
            $collection = $this->getAllByUid($uid)->getCollection();
            $entities = $collection?->getAll() ?? [];
            $ids = array_filter(array_map(fn($entity) => $entity->id, $entities));

            if (empty($ids)) {
                return DepositResult::success([]);
            }

            $placeholders = implode(', ', array_map(fn($i) => ":id_$i", array_keys($ids)));
            $params = [];
            foreach ($ids as $index => $id) {
                $params["id_$index"] = $id;
            }

            $sql = sprintf("DELETE FROM %s WHERE id IN (%s)", $this->getTableName(), $placeholders);
            $this->repository->executeSql($sql, $params);

            return DepositResult::success($ids);
        } catch (\Throwable $e) {
            return DepositResult::failure($e);
        }
    }

    public function getById(int $id): IDepositResult
    {
        try {
            $result = parent::getById($id);
            $dto = $result->getData();

            if (!$dto instanceof DbInvDepositDto) {
                throw new \RuntimeException("Deposit with ID $id not found or invalid DTO.");
            }

            $entity = InvDepositMapper::fromDbToEntity($dto);
            return DepositResult::success($entity);
        } catch (\Throwable $e) {
            return DepositResult::failure($e);
        }
    }
}
