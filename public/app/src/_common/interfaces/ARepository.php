<?php

namespace crm\src\_common\interfaces;

use PDO;
use Psr\Log\LoggerInterface;
use crm\src\_common\interfaces\IRepository;
use crm\src\services\Repositories\DbRepository\DbRepository;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;
use crm\src\components\UserManagement\_common\mappers\UserMapper;

/**
 * @template   TEntity of object
 * @implements IRepository<TEntity>
 */
abstract class ARepository implements IRepository
{
    protected DbRepository $repository;

    public function __construct(
        PDO $pdo,
        protected LoggerInterface $logger
    ) {
        $this->repository = new DbRepository($pdo, $logger);
    }

    /**
     * Абстрактное имя таблицы
     */
    abstract protected function getTableName(): string;

    /**
     * Абстрактное имя класса сущности
     */
    abstract protected function getEntityClass(): string;

    /**
     * Возвращает callable для маппинга массива в объект
     * "Пример": [UserMapper::class, 'fromArray']
     */
    abstract protected function fromArray(): callable;

    /**
     * Абстрактный преобразователь сущности в массив для сохранения
     */
    abstract protected function toArray(object $entity): array;

    public function save(object $entity): ?int
    {
        return $this->repository->executeQuery(
            (new QueryBuilder())->table($this->getTableName())->insert($this->toArray($entity))
        )->getInt();
    }

    public function update(object $entity): ?int
    {
        $data = $this->toArray($entity);
        $id = $data['id'] ?? throw new \InvalidArgumentException('Поле "id" обязательно для update()');

        return $this->repository->executeQuery(
            (new QueryBuilder())->table($this->getTableName())
                ->where('id = :id')
                ->update($data)
        )->getInt();
    }

    public function deleteById(int $id): ?int
    {
        return $this->repository->executeQuery(
            (new QueryBuilder())->table($this->getTableName())
                ->where('id = :id')
                ->delete(['id' => $id])
        )->getInt();
    }

    public function getById(int $id): ?object
    {
        return $this->repository->executeQuery(
            (new QueryBuilder())->table($this->getTableName())
                ->where('id = :id')
                ->limit(1)
                ->select(['id' => $id])
        )->first()->getObjectOrNullWithMapper($this->getEntityClass(), $this->fromArray());
    }

    public function getAll(): array
    {
        return $this->repository->executeQuery(
            (new QueryBuilder())->table($this->getTableName())->select()
        )->getValidMappedList($this->fromArray());
    }

    public function getColumnNames(): array
    {
        $sql = "
            SELECT COLUMN_NAME
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = :table
            ORDER BY ORDINAL_POSITION
        ";

        $result = $this->repository->executeSql($sql, ['table' => $this->getTableName()]);
        if (!$result->isSuccess()) {
            $this->logger->warning("Не удалось получить столбцы таблицы '{$this->getTableName()}': " . $result->getError()?->getMessage());
            return [];
        }

        $data = $result->getArrayOrNull() ?? [];
        return array_column($data, 'COLUMN_NAME');
    }
}
