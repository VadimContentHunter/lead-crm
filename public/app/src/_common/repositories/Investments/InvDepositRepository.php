<?php

namespace crm\src\_common\repositories\Investments;

use crm\src\_common\repositories\AResultRepository;
use crm\src\Investments\InvDeposit\_common\DTOs\DbInvDepositDto;
use crm\src\Investments\InvDeposit\_common\interfaces\IInvDepositRepository;
use crm\src\Investments\InvDeposit\_common\interfaces\IInvDepositResult;
use crm\src\Investments\InvDeposit\_mappers\InvDepositMapper;
use crm\src\Investments\InvDeposit\_common\InvDepositCollection;
use crm\src\Investments\InvDeposit\_common\adapters\InvDepositResult;

/**
 * Репозиторий для депозитов.
 *
 * @extends AResultRepository<DbInvDepositDto>
 */
class InvDepositRepository extends AResultRepository implements IInvDepositRepository
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
     * @return class-string<IInvDepositResult>
     */
    protected function getResultClass(): string
    {
        return InvDepositResult::class;
    }

    public function getAllByUid(string $uid): IInvDepositResult
    {
        try {
            $dtoList = $this->getAllByColumnValues('uid', [$uid])->getArray();

            $entities = array_map(
                fn(DbInvDepositDto $dto) => InvDepositMapper::fromDbToEntity($dto),
                $dtoList
            );

            return InvDepositResult::success(new InvDepositCollection($entities));
        } catch (\Throwable $e) {
            return InvDepositResult::failure($e);
        }
    }

    public function deleteAllByUid(string $uid): IInvDepositResult
    {
        try {
            $collection = $this->getAllByUid($uid)->getCollection();
            $entities = $collection?->getAll() ?? [];
            $ids = array_filter(array_map(fn($entity) => $entity->id, $entities));

            if (empty($ids)) {
                return InvDepositResult::success([]);
            }

            $placeholders = implode(', ', array_map(fn($i) => ":id_$i", array_keys($ids)));
            $params = [];
            foreach ($ids as $index => $id) {
                $params["id_$index"] = $id;
            }

            $sql = sprintf("DELETE FROM %s WHERE id IN (%s)", $this->getTableName(), $placeholders);
            $this->repository->executeSql($sql, $params);

            return InvDepositResult::success($ids);
        } catch (\Throwable $e) {
            return InvDepositResult::failure($e);
        }
    }

    public function getById(int $id): IInvDepositResult
    {
        try {
            $result = parent::getById($id);
            $dto = $result->getData();

            if (!$dto instanceof DbInvDepositDto) {
                throw new \RuntimeException("InvDeposit with ID $id not found or invalid DTO.");
            }

            $entity = InvDepositMapper::fromDbToEntity($dto);
            return InvDepositResult::success($entity);
        } catch (\Throwable $e) {
            return InvDepositResult::failure($e);
        }
    }
}
