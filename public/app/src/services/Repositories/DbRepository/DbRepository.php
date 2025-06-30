<?php

namespace crm\src\services\Repositories\DbRepository;

use PDO;
use PDOException;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;
use crm\src\services\Repositories\DbRepository\common\adapter\RepoResult;
use crm\src\services\Repositories\DbRepository\common\interfaces\IRepoResult;
use crm\src\services\Repositories\DbRepository\common\interfaces\IRepository;
use crm\src\services\Repositories\DbRepository\common\interfaces\IQueryStructure;

class DbRepository implements IRepository
{
    public function __construct(
        protected PDO $pdo,
        protected LoggerInterface $logger = new NullLogger()
    ) {
    }

    /**
     * Выполнить произвольный SQL-запрос с параметрами.
     *
     * Пример использования:
     * ```php
     * $sql = "SELECT * FROM users WHERE age > :age";
     * $params = ['age' => 18];
     * $result = $dbRepository->executeSql($sql, $params);
     *
     * if ($result->isSuccess()) {
     *  #code
     * }
     * ```
     *
     * @param string $sql    SQL-запрос с плейсхолдерами (:param)
     * @param array<string,mixed>  $params Параметры для привязки к запросу
     */
    public function executeSql(string $sql, array $params = []): IRepoResult
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $success = $stmt->execute($params);

            if (stripos(trim($sql), 'SELECT') === 0) {
                $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                return RepoResult::success($data);
            }

            return RepoResult::success($success);
        } catch (\PDOException $e) {
            $this->logger->error("Ошибка выполнения SQL: {$e->getMessage()}");
            return RepoResult::failure($e);
        }
    }


    public function executeQuery(IQueryStructure $query): IRepoResult
    {
        try {
            $table = is_string($query->getTable()) ? $this->sanitizeIdentifier($query->getTable()) : null;
            $action = $query->getAction();
            $payload = $query->getPayload();
            $wheres = $query->getWheres();
            $orderBy = $query->getOrderBy();
            $limit = $query->getLimit();
            $bindings = $query->getBindings();

            if (!$table || !$action) {
                $message = "Не указана таблица или действие.";
                $this->logger->error($message);
                return RepoResult::failure(new InvalidArgumentException($message));
            }

            return match ($action) {
                'insert' => RepoResult::success($this->executeInsert($table, $payload)),
                'update' => RepoResult::success($this->executeUpdate($table, $payload, $wheres, $bindings)),
                'delete' => RepoResult::success($this->executeDelete($table, $wheres, $bindings)),
                'select' => RepoResult::success($this->executeSelect($table, $wheres, $orderBy, $limit, $bindings)),
                default   => RepoResult::failure(new InvalidArgumentException("Неизвестное действие: $action")),
            };
        } catch (PDOException $e) {
            $this->logger->error("Ошибка выполнения запроса: " . $e->getMessage());
            return RepoResult::failure($e);
        }
    }

    /**
     * @param array<string,mixed> $data Метод сам сделает из них плейсхолдеры param => :param
     *
     * @return int Возвращает ID вставленного объекта
     */
    protected function executeInsert(string $table, array $data): int
    {
        // Очистка данных перед вставкой
        $data = array_map([$this, 'sanitizeValue'], $data);

        $columns = array_map([$this, 'sanitizeIdentifier'], array_keys($data));
        $placeholders = array_map(fn($key) => ":$key", array_keys($data));

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * @param array<string,mixed> $data       и данные для обновления, и параметры для WHERE
     *                                        Метод сам сделает из них плейсхолдеры param => :param
     * @param string[] $conditions условия с плейсхолдерами, например ["id = :id"]
     */
    protected function executeUpdate(string $table, array $data, array $conditions): bool
    {
        $data = array_map([$this, 'sanitizeValue'], $data);

        $set = implode(', ', array_map(
            fn($col) => $this->sanitizeIdentifier($col) . " = :$col",
            // Берём только ключи, которые не используются в условиях
            // Но проще — пусть будут все, главное чтобы плейсхолдеры в conditions совпадали с ключами в $data
            array_keys($data)
        ));

        $sql = "UPDATE $table SET $set " . $this->buildWhereClause($conditions);

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($data);
    }


    /**
     * @param string[] $conditions условия с плейсхолдерами
     * @param array<string,mixed> $data       параметры для условий
     */
    protected function executeDelete(string $table, array $conditions, array $data): bool
    {
        $data = array_map([$this, 'sanitizeValue'], $data);

        $sql = "DELETE FROM $table " . $this->buildWhereClause($conditions);

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * @param string[] $conditions условия с плейсхолдерами
     * @param array<string,mixed> $data       параметры для условий
     *
     * @return mixed[]
     */
    protected function executeSelect(string $table, array $conditions, ?array $orderBy, ?int $limit, array $data): array
    {
        $data = array_map([$this, 'sanitizeValue'], $data);

        $sql = "SELECT * FROM $table " . $this->buildWhereClause($conditions);

        if ($orderBy) {
            $col = $orderBy['column'] ?? 'id';
            $dir = $orderBy['direction'] ?? 'asc';
            $sql .= " ORDER BY " . $this->sanitizeIdentifier($col) . " " . strtoupper($dir);
        }

        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param string[] $conditions
     */
    protected function buildWhereClause(array $conditions): string
    {
        if (empty($conditions)) {
            return '';
        }

        $clauses = array_map(fn($c) => "($c)", $conditions);
        return "WHERE " . implode(' AND ', $clauses);
    }

    protected function sanitizeIdentifier(string $identifier): string
    {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $identifier)) {
            throw new InvalidArgumentException("Недопустимое имя: $identifier");
        }
        return "`$identifier`";
    }

    protected function sanitizeValue(mixed $value): mixed
    {
        if (is_string($value)) {
            // Очистить строки, например удалить управляющие символы
            $value = trim($value);
            $value = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            // Можно добавить дополнительные проверки: длина, кодировка и т.д.
        }
        // Для чисел, bool и др. можно добавить проверки или преобразования

        return $value;
    }
}
