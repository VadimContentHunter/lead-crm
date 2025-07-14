<?php

namespace crm\src\_common\repositories\Investments;

use crm\src\_common\repositories\AResultRepository;
use crm\src\Investments\Activity\_entities\InvActivity;
use crm\src\Investments\Activity\_mappers\ActivityMapper;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;
use crm\src\Investments\Activity\_common\InvActivityCollection;
use crm\src\Investments\Activity\_common\adapters\ActivityResult;
use crm\src\Investments\Activity\_common\interfaces\IActivityResult;
use crm\src\Investments\Activity\_common\interfaces\IActivityRepository;

/**
 * Репозиторий для инвестиционных сделок (activity).
 *
 * @extends AResultRepository<InvActivity>
 */
class ActivityRepository extends AResultRepository implements IActivityRepository
{
    /**
     * Возвращает название таблицы.
     */
    protected function getTableName(): string
    {
        return 'activities';
    }

    /**
     * @return class-string<InvActivity>
     */
    protected function getEntityClass(): string
    {
        return InvActivity::class;
    }

    /**
     * Возвращает callable, преобразующий массив в сущность.
     *
     * @return callable(array<string, mixed>): InvActivity
     */
    protected function fromArray(): callable
    {
        return function (array $data): InvActivity {
            $dto = ActivityMapper::fromArrayToDb($data);
            return ActivityMapper::fromDbToEntity($dto);
        };
    }


    /**
     * Преобразует сущность в массив данных для сохранения.
     *
     * @param  object $entity
     * @return array<string, mixed>
     */
    protected function toArray(object $entity): array
    {
        /**
         * @var InvActivity $entity
         */
        $dto = ActivityMapper::fromEntityToDb($entity);
        return ActivityMapper::fromDbToArray($dto);
    }

    /**
     * Возвращает класс результата.
     *
     * @return class-string<IActivityResult>
     */
    protected function getResultClass(): string
    {
        return ActivityResult::class;
    }

    /**
     * Возвращает все сделки по lead_uid.
     *
     * @param  int $leadId
     * @return IActivityResult
     */
    public function getAllByLeadId(int $leadId): IActivityResult
    {
        try {
            $activities = $this->getAllByColumnValues('lead_uid', [$leadId])->getArray();
            return ActivityResult::success(new InvActivityCollection($activities));
        } catch (\Throwable $e) {
            return ActivityResult::failure($e);
        }
    }

    /**
     * Возвращает все активные сделки по lead_uid.
     *
     * @param  int $leadId
     * @return IActivityResult
     */
    public function getAllActiveByLeadId(int $leadId): IActivityResult
    {
        try {
            $rows = $this->repository->executeQuery(
                (new QueryBuilder())
                    ->table($this->getTableName())
                    ->where('lead_uid = :lead_uid AND type = :type')
                    ->bindings(['lead_uid' => $leadId, 'type' => 'active'])
                    ->select()
            )->getValidMappedList($this->fromArray());

            return ActivityResult::success(new InvActivityCollection($rows));
        } catch (\Throwable $e) {
            return ActivityResult::failure($e);
        }
    }

    /**
     * Возвращает все закрытые сделки по lead_uid.
     *
     * @param  int $leadId
     * @return IActivityResult
     */
    public function getAllClosedByLeadId(int $leadId): IActivityResult
    {
        try {
            $rows = $this->repository->executeQuery(
                (new QueryBuilder())
                    ->table($this->getTableName())
                    ->where('lead_uid = :lead_uid AND type = :type')
                    ->bindings(['lead_uid' => $leadId, 'type' => 'closed'])
                    ->select()
            )->getValidMappedList($this->fromArray());

            return ActivityResult::success(new InvActivityCollection($rows));
        } catch (\Throwable $e) {
            return ActivityResult::failure($e);
        }
    }

    /**
     * Удаляет все сделки по lead_uid.
     *
     * @param  int $leadId
     * @return IActivityResult Список ID удалённых сделок или ошибка
     */
    public function deleteAllByLeadId(int $leadId): IActivityResult
    {
        try {
            $activities = $this->getAllByLeadId($leadId)->getCollection()?->getAll() ?? [];
            $ids = array_filter(array_map(fn(InvActivity $a) => $a->id, $activities));

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
