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
    /**
     * Возвращает имя таблицы для операций с базой данных.
     *
     * @return string
     */
    protected function getTableName(): string
    {
        return 'activities';
    }

    /**
     * Возвращает имя класса DTO-сущности.
     *
     * @return class-string<DbActivityDto>
     */
    protected function getEntityClass(): string
    {
        return DbActivityDto::class;
    }

    /**
     * Возвращает функцию, преобразующую массив данных из БД в DTO-сущность.
     *
     * @return callable(array<string, mixed>): DbActivityDto
     */
    protected function fromArray(): callable
    {
        return fn(array $data): DbActivityDto => ActivityMapper::fromArrayToDb($data);
    }

    /**
     * Преобразует DTO-сущность в массив для сохранения в базу данных.
     *
     * @param  object $entity Объект типа DbActivityDto
     * @return array<string, mixed>
     */
    protected function toArray(object $entity): array
    {
        /**
 * @var DbActivityDto $entity
*/
        return ActivityMapper::fromDbToArray($entity);
    }

    /**
     * Возвращает имя класса, реализующего обёртку результата.
     *
     * @return class-string<IActivityResult>
     */
    protected function getResultClass(): string
    {
        return ActivityResult::class;
    }

    /**
     * Получает все сделки, связанные с заданным lead_uid.
     *
     * @param  int $leadId Идентификатор лида
     * @return IActivityResult Коллекция сущностей сделок
     */
    public function getAllByLeadId(int $leadId): IActivityResult
    {
        try {
            $dtoList = $this->getAllByColumnValues('lead_uid', [$leadId])->getArray();

            $entities = array_map(
                fn(DbActivityDto $dto) => ActivityMapper::fromDbToEntity($dto),
                $dtoList
            );

            return ActivityResult::success(new InvActivityCollection($entities));
        } catch (\Throwable $e) {
            return ActivityResult::failure($e);
        }
    }

    /**
     * Получает все активные сделки для заданного lead_uid.
     *
     * @param  int $leadId Идентификатор лида
     * @return IActivityResult Коллекция активных сделок
     */
    public function getAllActiveByLeadId(int $leadId): IActivityResult
    {
        try {
            $dtoList = $this->repository->executeQuery(
                (new QueryBuilder())
                    ->table($this->getTableName())
                    ->where('lead_uid = :lead_uid AND type = :type')
                    ->bindings(['lead_uid' => $leadId, 'type' => 'active'])
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

    /**
     * Получает все закрытые сделки для заданного lead_uid.
     *
     * @param  int $leadId Идентификатор лида
     * @return IActivityResult Коллекция закрытых сделок
     */
    public function getAllClosedByLeadId(int $leadId): IActivityResult
    {
        try {
            $dtoList = $this->repository->executeQuery(
                (new QueryBuilder())
                    ->table($this->getTableName())
                    ->where('lead_uid = :lead_uid AND type = :type')
                    ->bindings(['lead_uid' => $leadId, 'type' => 'closed'])
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

    /**
     * Удаляет все сделки, связанные с заданным lead_uid.
     *
     * @param  int $leadId Идентификатор лида
     * @return IActivityResult Список ID удалённых сделок или ошибка
     */
    public function deleteAllByLeadId(int $leadId): IActivityResult
    {
        try {
            $collection = $this->getAllByLeadId($leadId)->getCollection();
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
