<?php

namespace crm\src\_common\interfaces;

use Throwable;
use PDO;
use crm\src\_common\interfaces\IResult;
use crm\src\_common\interfaces\IResultRepository;
use crm\src\_common\interfaces\AResult;
use crm\src\services\Repositories\DbRepository\DbRepository;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;

/**
 * Базовый репозиторий с поддержкой возвращаемого результата в виде IResult.
 *
 * @template   TEntity of object
 * @implements IResultRepository<TEntity>
 */
abstract class AResultRepository implements IResultRepository
{
    protected DbRepository $repository;

    public function __construct(
        PDO $pdo,
        protected \Psr\Log\LoggerInterface $logger
    ) {
        $this->repository = new DbRepository($pdo, $logger);
    }

    /**
     * Имя таблицы.
     */
    abstract protected function getTableName(): string;

    /**
     * Класс сущности.
     *
     * @return class-string<TEntity>
     */
    abstract protected function getEntityClass(): string;

    /**
     * Маппер массива в объект.
     *
     * @return callable(array<string, mixed>): TEntity
     */
    abstract protected function fromArray(): callable;

    /**
     * Преобразует сущность в массив.
     *
     * @param  TEntity $entity
     * @return array<string, mixed>
     */
    abstract protected function toArray(object $entity): array;

    /**
     * Класс для возврата результата.
     *
     * @return class-string<IResult>
     */
    protected function getResultClass(): string
    {
        return AResult::class;
    }

    protected function wrapSuccess(mixed $data): IResult
    {
        $resultClass = $this->getResultClass();
        return $resultClass::success($data);
    }

    protected function wrapFailure(Throwable $e): IResult
    {
        $resultClass = $this->getResultClass();
        return $resultClass::failure($e);
    }

    public function save(object|array $entity): IResult
    {
        try {
            $data = is_object($entity) ? $this->toArray($entity) : $entity;
            $resId = $this->repository->executeQuery(
                (new QueryBuilder())
                    ->table($this->getTableName())
                    ->insert($data)
            );

            if ($resId->isSuccess()) {
                return $this->wrapSuccess($resId->getInt());
            }

            return $this->wrapFailure($resId->getError() ?? new \RuntimeException("Не удалось сохранить"));
        } catch (Throwable $e) {
            return $this->wrapFailure($e);
        }
    }


    /**
     * Обновляет сущность или ассоциативный массив данных в репозитории.
     *
     * Принимает объект сущности (TEntity) или массив. Если объект не соответствует ожидаемому типу,
     * или отсутствует поле 'id', или нет данных для обновления — возвращается IResult с ошибкой.
     *
     * @param  TEntity|array<string,mixed> $entityOrData
     * @return IResult Результат обновления: ID или ошибка.
     */
    public function update(object|array $entityOrData): IResult
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

            if (!isset($data['id'])) {
                return $this->wrapFailure(new \InvalidArgumentException("Поле 'id' обязательно для update()"));
            }

            $bindings = $data;
            unset($data['id']);

            if (empty($data)) {
                return $this->wrapFailure(new \RuntimeException("Нет данных для обновления"));
            }

            $result = $this->repository->executeQuery(
                (new QueryBuilder())
                ->table($this->getTableName())
                ->where('id = :id')
                ->bindings($bindings)
                ->update($data)
            );

            if (!$result->isSuccess()) {
                return $this->wrapFailure($result->getError() ?? new \RuntimeException("Не удалось обновить"));
            }

            return $this->wrapSuccess($result->getInt());
        } catch (Throwable $e) {
            return $this->wrapFailure($e);
        }
    }


    public function deleteById(int $id): IResult
    {
        try {
            $result = $this->repository->executeQuery(
                (new QueryBuilder())
                    ->table($this->getTableName())
                    ->where('id = :id')
                    ->delete(['id' => $id])
            )->getInt();

            return $this->wrapSuccess($result);
        } catch (Throwable $e) {
            return $this->wrapFailure($e);
        }
    }

    public function getById(int $id): IResult
    {
        try {
            $entity = $this->repository->executeQuery(
                (new QueryBuilder())
                    ->table($this->getTableName())
                    ->where('id = :id')
                    ->limit(1)
                    ->select(['id' => $id])
            );

            if (!$entity->isSuccess() || $entity->isEmpty()) {
                return $this->wrapFailure($entity->getError() ?? new \RuntimeException("Не удалось получить"));
            }

            return $this->wrapSuccess($entity->first()->getObjectOrNullWithMapper($this->getEntityClass(), $this->fromArray()));
        } catch (Throwable $e) {
            return $this->wrapFailure($e);
        }
    }

    public function getAll(): IResult
    {
        try {
            $result = $this->repository->executeQuery(
                (new QueryBuilder())
                    ->table($this->getTableName())
                    ->select()
            )->getValidMappedList($this->fromArray());

            return $this->wrapSuccess($result);
        } catch (Throwable $e) {
            return $this->wrapFailure($e);
        }
    }

    public function getColumnNames(): IResult
    {
        try {
            $sql = "
                SELECT COLUMN_NAME
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = :table
                ORDER BY ORDINAL_POSITION
            ";

            $rows = $this->repository->executeSql($sql, ['table' => $this->getTableName()]);
            $columns = $rows->getArrayOrNull() ?? [];

            return $this->wrapSuccess(array_column($columns, 'COLUMN_NAME'));
        } catch (Throwable $e) {
            return $this->wrapFailure($e);
        }
    }

    public function getAllExcept(string $column = '', array $excludedValues = []): IResult
    {
        try {
            if (empty($excludedValues)) {
                return $this->getAll();
            }

            $placeholders = [];
            $params = [];
            foreach ($excludedValues as $i => $val) {
                $param = "excluded_$i";
                $placeholders[] = ":$param";
                $params[$param] = $val;
            }

            $sql = sprintf(
                "SELECT * FROM %s WHERE %s NOT IN (%s)",
                $this->getTableName(),
                $column,
                implode(', ', $placeholders)
            );

            $result = $this->repository->executeSql($sql, $params)
                ->getValidMappedList($this->fromArray());

            return $this->wrapSuccess($result);
        } catch (Throwable $e) {
            return $this->wrapFailure($e);
        }
    }

    /**
     * Получает все записи из таблицы, у которых значение в указанной колонке входит в указанный список.
     *
     * @param string $column Имя колонки таблицы (например, 'lead_uid')
     * @param array<int, scalar> $values Список значений, по которым искать (например, ['uid_1', 'uid_2'])
     *
     * @return IResult Результат, содержащий массив DTO в ->getArray()
     */
    public function getAllByColumnValues(string $column = '', array $values = []): IResult
    {
        try {
            if (empty($column) || empty($values)) {
                return $this->getAll();
            }

            $placeholders = [];
            $params = [];
            foreach ($values as $i => $val) {
                $param = "val_$i";
                $placeholders[] = ":$param";
                $params[$param] = $val;
            }

            $sql = sprintf(
                "SELECT * FROM %s WHERE %s IN (%s)",
                $this->getTableName(),
                $column,
                implode(', ', $placeholders)
            );

            $result = $this->repository->executeSql($sql, $params)
                ->getValidMappedList($this->fromArray());

            return $this->wrapSuccess($result);
        } catch (Throwable $e) {
            return $this->wrapFailure($e);
        }
    }
}
