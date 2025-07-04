<?php

namespace crm\src\_common\repositories\LeadRepository;

use PDO;
use Psr\Log\LoggerInterface;
use crm\src\components\LeadManagement\_entities\Lead;
use crm\src\services\Repositories\DbRepository\DbRepository;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;
use crm\src\components\LeadManagement\_common\DTOs\SourceDto;
use crm\src\components\LeadManagement\_common\DTOs\StatusDto;
use crm\src\components\LeadManagement\_common\DTOs\LeadInputDto;
use crm\src\components\LeadManagement\_common\DTOs\LeadFilterDto;
use crm\src\components\LeadManagement\_common\mappers\LeadDbMapper;
use crm\src\components\LeadManagement\_common\DTOs\AccountManagerDto;
use crm\src\components\LeadManagement\_common\mappers\LeadInputMapper;
use crm\src\components\LeadManagement\_common\interfaces\ILeadRepository;

/**
 * @extends ARepository<LeadInputDto>
 */
class LeadRepository implements ILeadRepository
{
    protected DbRepository $repository;

    public function __construct(
        PDO $pdo,
        protected LoggerInterface $logger
    ) {
        $this->repository = new DbRepository($pdo, $logger);
    }

    protected function getTableName(): string
    {
        return 'leads';
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

    public function save(object $entity): ?int
    {
        if (!$entity instanceof Lead) {
            return null;
        }

        $dto = LeadInputMapper::fromEntity($entity);
        // $data = LeadInputMapper::toArray($dto);
        // $data['created_at'] = date('Y-m-d H:i:s');

        $query = (new QueryBuilder())
            ->table($this->getTableName())
            ->insert(LeadDbMapper::fromInputDtoToArray($dto));

        $result = $this->repository->executeQuery($query);
        return $result->isSuccess() ? $result->getInt() : null;
    }

    public function update(object $entity): ?int
    {
        if (!$entity instanceof Lead) {
            return null;
        }

        if (!$entity->id) {
            return null;
        }

        $data = LeadDbMapper::fromEntityToArray($entity);
        // unset($data['id']);

        $query = (new QueryBuilder())
            ->table($this->getTableName())
            ->where('id = :id')
            ->update($data);


        $result = $this->repository->executeQuery($query);
        return $result->isSuccess() ? $entity->id : null;
    }

    public function deleteById(int $id): ?int
    {
        $query = (new QueryBuilder())
        ->table($this->getTableName())
        ->where('id = :id')
        ->delete(['id' => $id]);

        $result = $this->repository->executeQuery($query);
        return $result->isSuccess() ? $id : null;
    }

    public function getById(int $id): ?Lead
    {
        $query = (new QueryBuilder())
            ->table($this->getTableName())
            ->where('id = :id')
            ->select(['id' => $id]);

        $result = $this->repository->executeQuery($query);
        $data = $result->getArrayOrNull();

        return !empty($data) ? $this->mapToLead($data[0]) : null;
    }

    public function getAll(): array
    {
        $query = (new QueryBuilder())
        ->table($this->getTableName())
        ->select();

        $result = $this->repository->executeQuery($query);
        $rows = $result->getArrayOrNull() ?? [];

        return array_map(fn($row) => $this->mapToLead($row), $rows);
    }

    public function deleteByAccountManagerId(int $accountManagerId): ?int
    {
        $query = (new QueryBuilder())
        ->table($this->getTableName())
        ->where('account_manager_id = :id')
        ->delete(['id' => $accountManagerId]);

        $result = $this->repository->executeQuery($query);
        return $result->isSuccess() ? $result->getInt() : null;
    }

    public function getLeadsByManagerId(int $managerId): array
    {
        return $this->getLeadsByField('account_manager_id', $managerId);
    }

    public function getLeadsBySourceId(int $sourceId): array
    {
        return $this->getLeadsByField('source_id', $sourceId);
    }

    public function getLeadsByStatusId(int $statusId): array
    {
        return $this->getLeadsByField('status_id', $statusId);
    }

    /**
     * Универсальный метод для выборки по полю.
     */
    protected function getLeadsByField(string $field, int $value): array
    {
        $query = (new QueryBuilder())
        ->table($this->getTableName())
        ->where("$field = :val", ['val' => $value])
        ->select();

        $result = $this->repository->executeQuery($query);
        $rows = $result->getArrayOrNull() ?? [];

        return array_map(fn($row) => $this->mapToLead($row), $rows);
    }

    /**
     * Маппинг данных из БД в сущность Lead
     */
    protected function mapToLead(array $data): Lead
    {
        return new Lead(
            fullName: (string)($data['full_name'] ?? ''),
            contact: (string)($data['contact'] ?? ''),
            address: (string)($data['address'] ?? ''),
            source: isset($data['source_id']) ? new SourceDto((int)$data['source_id'], '') : null,
            status: isset($data['status_id']) ? new StatusDto((int)$data['status_id'], '') : null,
            accountManager: isset($data['account_manager_id']) ? new AccountManagerDto((int)$data['account_manager_id'], '') : null,
            createdAt: isset($data['created_at']) ? new \DateTime($data['created_at']) : null,
            id: isset($data['id']) ? (int)$data['id'] : null,
        );
    }

    /**
     * @return mixed[]
     */
    public function getFilteredLeads(LeadFilterDto $filter): array
    {
        $params = [];

        $isPotentialSet = is_numeric($filter->potentialMin) && $filter->potentialMin > 0;
        $isBalanceSet = is_numeric($filter->balanceMin) && $filter->balanceMin > 0;
        $isDrainSet = is_numeric($filter->drainMin) && $filter->drainMin > 0;

        // Баланс нужен только если хотя бы один параметр > 0
        $joinBalances = $isPotentialSet || $isBalanceSet || $isDrainSet;

        $sql = <<<SQL
            SELECT leads.*
            FROM leads
            LEFT JOIN statuses ON statuses.id = leads.status_id
            LEFT JOIN sources ON sources.id = leads.source_id
            LEFT JOIN users ON users.id = leads.account_manager_id
        SQL;

        if ($joinBalances) {
            $sql .= ' LEFT JOIN balances ON balances.lead_id = leads.id';
        }

        $sql .= ' WHERE 1 = 1';

        // Баланс-фильтры
        if ($joinBalances) {
            if ($isPotentialSet) {
                $sql .= " AND balances.potential >= :potential_min";
                $params['potential_min'] = $filter->potentialMin;
            }

            if ($isBalanceSet) {
                $sql .= " AND balances.current >= :balance_min";
                $params['balance_min'] = $filter->balanceMin;
            }

            if ($isDrainSet) {
                $sql .= " AND balances.drain >= :drain_min";
                $params['drain_min'] = $filter->drainMin;
            }
        }

        // Фильтры
        if (!empty($filter->search)) {
            $sql .= " AND (leads.full_name LIKE :searchFullName OR leads.contact LIKE :searchContact)";
            $params['searchFullName'] = '%' . $filter->search . '%';
            $params['searchContact'] = '%' . $filter->search . '%';
        }

        if (!empty($filter->manager)) {
            $sql .= " AND users.id = :manager";
            $params['manager'] = $filter->manager;
        }

        if (!empty($filter->status)) {
            $sql .= " AND statuses.id = :status";
            $params['status'] = $filter->status;
        }

        if (!empty($filter->source)) {
            $sql .= " AND sources.id = :source";
            $params['source'] = $filter->source;
        }

        // Сортировка
        $allowedSortFields = [
            'leads.id', 'leads.full_name', 'leads.address',
            'statuses.title', 'sources.title', 'users.login',
            'balances.current', 'balances.drain', 'balances.potential'
        ];

        $sortBy = in_array($filter->sort, $allowedSortFields, true) ? $filter->sort : 'leads.id';
        $sortDir = strtolower($filter->sortDir ?? '') === 'desc' ? 'DESC' : 'ASC';
        $sql .= " ORDER BY $sortBy $sortDir";

        // Выполнение
        $result = $this->repository->executeSql($sql, $params);

        return $result->getArrayOrNull() ?? [];
    }
}
