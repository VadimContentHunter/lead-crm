<?php

namespace crm\src\_common\adapters\Security\BasedAccessGranter;

use crm\src\controllers\LeadPage;
use crm\src\controllers\UserPage;
use crm\src\controllers\SourcePage;
use crm\src\controllers\StatusPage;
use crm\src\components\Security\RoleNames;
use crm\src\controllers\API\LeadController;
use crm\src\controllers\API\UserController;
use crm\src\controllers\API\SourceController;
use crm\src\controllers\API\StatusController;
use crm\src\components\LeadManagement\GetLead;
use crm\src\components\UserManagement\GetUser;
use crm\src\components\LeadManagement\_entities\Lead;
use crm\src\components\Security\_entities\AccessSpace;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_handlers\HandleAccessRole;
use crm\src\components\Security\_handlers\HandleAccessSpace;
use crm\src\components\Security\_exceptions\SecurityException;
use crm\src\_common\repositories\LeadRepository\LeadRepository;
use crm\src\components\LeadManagement\_common\DTOs\LeadFilterDto;
use crm\src\components\UserManagement\_common\DTOs\UserFilterDto;
use crm\src\components\UserManagement\_common\mappers\UserMapper;
use crm\src\components\Security\_common\DTOs\AccessFullContextDTO;
use crm\src\components\LeadManagement\_common\DTOs\AccountManagerDto;
use crm\src\components\Security\_exceptions\JsonRpcSecurityException;
use crm\src\components\UserManagement\_common\mappers\UserFilterMapper;
use crm\src\components\Security\_common\mappers\AccessFullContextMapper;
use crm\src\components\LeadManagement\_common\interfaces\ILeadRepository;
use crm\src\components\Security\_common\interfaces\IAccessRoleRepository;
use crm\src\components\UserManagement\_common\interfaces\IUserRepository;
use crm\src\components\Security\_common\interfaces\IAccessSpaceRepository;
use crm\src\_common\adapters\Security\BasedAccessGranter\IRoleAccessHandler;
use crm\src\components\Security\_common\interfaces\IAccessContextRepository;

class ManagerRoleHandler implements IRoleAccessHandler
{
    public function __construct(
        // private IAccessContextRepository $contextRepository,
        private IAccessRoleRepository $roleRepository,
        private IAccessSpaceRepository $spaceRepository,
        private IUserRepository $userRepository,
        private ILeadRepository $leadRepository
    ) {
    }

    public function supports(object $target, string $methodName, AccessFullContextDTO $context): bool
    {
        return RoleNames::isManager($context->role->name ?? '');
    }

    public function handle(AccessFullContextDTO $context, object $target, string $methodName, array $args): mixed
    {
        if ($target instanceof HandleAccessSpace && $methodName === 'getAllSpaces') {
            return $target->getAllSpaces('id', [$context->getSpaceId() ?? 0]);
        }

        if ($target instanceof HandleAccessRole && $methodName === 'getAllExceptRoles') {
            return $this->roleRepository->getAllByColumnValues('name', [RoleNames::MANAGER->value]);
        }

        if ($target instanceof GetUser && $methodName === 'executeAllMapped') {
            return $target->executeById($context->userId)
                ->mapToNew(fn(mixed $data) => [UserMapper::toArray($data)]);
        }

        if ($target instanceof UserController) {
            return $this->handleUserController($context, $target, $methodName, $args);
        }

        if ($target instanceof GetUser && $methodName === 'filtered') {
            $userLogin = $this->userRepository->getById($context->userId)?->login;
            $userFilterDto = $args[0] instanceof UserFilterDto ? $args[0] : UserFilterMapper::fromArray($args[0] ?? []);
            $userFilterDto->login = $userLogin ?? $userFilterDto->login ?? '';

            return $target->filtered($userFilterDto);
        }

        if ($target instanceof UserPage && $methodName === 'showEditUserPage') {
            throw new SecurityException("Менеджер не может редактировать пользователей.");
        }

        if ($target instanceof StatusController || $target instanceof SourceController) {
            $entity = $target instanceof StatusController ? 'статусы' : 'источники';
            $this->denyIfIn($methodName, ['createStatus', 'deleteStatus', 'createSource', 'deleteSource'], "Менеджер не может {$this->actionLabel($methodName)} {$entity}.");
        }

        if ($target instanceof StatusPage && $methodName === 'showAddStatusPage') {
            throw new SecurityException("Менеджер не может посетить страницу создания статуса.");
        }

        if ($target instanceof SourcePage && $methodName === 'showAddSourcePage') {
            throw new SecurityException("Менеджер не может посетить страницу создания источника.");
        }

        if ($target instanceof LeadController && $methodName === 'createLead') {
            $leadAccountManagerId = $args[0]['accountManagerId'] ?? null;
            if (filter_var($leadAccountManagerId, FILTER_VALIDATE_INT) === false) {
                return $target->$methodName(...$args);
            }

            $leadSpace = $this->spaceRepository->getById($leadAccountManagerId ?? 0);
            $thisSpace = $this->spaceRepository->getById($context->getSpaceId() ?? 0);
            if ($leadSpace?->id !== $thisSpace?->id) {
                throw new JsonRpcSecurityException("Менеджер может создавать лиды только в пространстве своего менеджера или в своем.");
            }
        }

        if ($target instanceof LeadRepository && $methodName === 'getAll') {
            $res = $target->getLeadsByManagerId($context->userId);
            foreach ($res as $lead) {
                $lead->groupName = $context->getSpaceName();
            }
            return $res;
            // $filter = new LeadFilterDto(manager: $context->userId);
            // return $this->getFilteredLeads($target, $filter, AccessFullContextMapper::toAccessContext($context), $context?->space);
        }

        if ($target instanceof LeadRepository && $methodName === 'getFilteredLeads') {
            $filter = $args[0] instanceof LeadFilterDto ? $args[0] : new LeadFilterDto(manager: (string)$context->userId);
            $filter->manager = (string)$context->userId;
            return $this->getFilteredLeads($target, $filter, AccessFullContextMapper::toAccessContext($context), $context->space);
        }

        if ($target instanceof LeadPage && $methodName === 'showEditLeadPage') {
            $leadId = $args[0] ?? null;
            $lead = $this->leadRepository->getById($leadId);
            if (
                $lead instanceof Lead
                && $lead->accountManager instanceof AccountManagerDto
                && $lead->accountManager->id !== null
                && $lead->accountManager->id > 0
            ) {
                $leadAccountManagerId = $lead->accountManager->id;
                if ($leadAccountManagerId === $context->userId) {
                    return $target->$methodName(...$args);
                }
            }
            throw new SecurityException("У вас нет прав на редактирование данного лида.");
        }

        return $target->$methodName(...$args);
    }

    /**
     * @return mixed[]
     */
    public function getFilteredLeads(LeadRepository $leadRepository, LeadFilterDto $filter, AccessContext $accessContext, ?AccessSpace $space = null): array
    {
        $params = [];

        $isPotentialSet = is_numeric($filter->potentialMin) && $filter->potentialMin > 0;
        $isBalanceSet = is_numeric($filter->balanceMin) && $filter->balanceMin > 0;
        $isDrainSet = is_numeric($filter->drainMin) && $filter->drainMin > 0;

        $sql = <<<SQL
            SELECT leads.*, access_spaces.name AS group_name
            FROM leads
            LEFT JOIN statuses ON statuses.id = leads.status_id
            LEFT JOIN sources ON sources.id = leads.source_id
            LEFT JOIN users ON users.id = leads.account_manager_id
            LEFT JOIN access_contexts ON access_contexts.user_id = leads.account_manager_id
            LEFT JOIN access_spaces ON access_spaces.id = access_contexts.space_id
        SQL;

        $sql .= ' WHERE 1 = 1';

        if ($space !== null) {
            $sql .= " AND access_spaces.id = :space_id";
            $params['space_id'] = $space->id;
        } else {
            $sql .= " AND leads.account_manager_id = :manager_id";
            $params['manager_id'] = $accessContext->userId;
        }

        if ($isPotentialSet || $isBalanceSet || $isDrainSet) {
            $sql .= ' LEFT JOIN balances ON balances.lead_id = leads.id';

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

        if (!empty($filter->groupName)) {
            $sql .= " AND access_spaces.name = :space";
            $params['space'] = $filter->groupName;
        }

        $allowedSortFields = [
        'leads.id', 'leads.full_name', 'leads.address',
        'statuses.title', 'sources.title', 'users.login',
        'balances.current', 'balances.drain', 'balances.potential',
        'access_spaces.name'
        ];

        $sortBy = in_array($filter->sort, $allowedSortFields, true) ? $filter->sort : 'leads.id';
        $sortDir = strtolower($filter->sortDir ?? '') === 'desc' ? 'DESC' : 'ASC';
        $sql .= " ORDER BY $sortBy $sortDir";

        $result = $leadRepository->repository->executeSql($sql, $params);

        return $result->getArrayOrNull() ?? [];
    }

    /**
     * @param array<int,mixed> $args
     */
    private function handleUserController(AccessFullContextDTO $context, UserController $target, string $method, array $args): mixed
    {
        if (in_array($method, ['deleteUser', 'deleteUserById', 'editUser'], true)) {
            throw new JsonRpcSecurityException("Менеджер не может {$this->actionLabel($method)} пользователей.");
        }

        if (in_array($method, ['filterUsers', 'filterUsersFormatTable'], true)) {
            $userLogin = $this->userRepository->getById($context->userId)?->login;
            $argSearch = $args[0]['search'] ?? '';

            $login = $userLogin ?? '--';
            $search = ($argSearch !== $login && (int)$argSearch !== $context->userId) ? '--' : $argSearch;
            $search = $argSearch === '' ? $login : $search;

            $target->filterUsersFormatTable(['login' => $login, 'search' => $search]);
            // Возврата не требуется filterUsersFormatTable:void
        }

        if ($method === 'createUser') {
            $role_id = $args[0]['role_id'] ?? null;
            $space_id = $args[0]['space_id'] ?? null;

            $role = $this->roleRepository->getById($role_id);
            if ($role === null) {
                $target->createUser($args[0]);
                // Возврата не требуется createUser:void
            }

            if ($space_id !== null) {
                $space = $this->spaceRepository->getById($space_id);
            }

            // 3. Проверка ограничений для выбранной роли
            if (!RoleNames::isManager($role?->name ?? '')) {
                throw new JsonRpcSecurityException("Данная роль не предназначена для создания пользователей.");
            }

            // 4. Проверка пространства для роли менеджера
            if ($context->getSpaceId() !== $space?->id) {
                throw new JsonRpcSecurityException("Менеджер может добавлять только в свое пространство.");
            }

            $args[0]['space_id'] = $space?->id;
            $args[0]['role_id'] = $role?->id ?? null;

            $target->createUser($args[0]);
            // Возврата не требуется createUser:void
        }

        return $target->$method(...$args);
    }

    /**
     * @param string[] $restricted
     */
    private function denyIfIn(string $method, array $restricted, string $message): void
    {
        if (in_array($method, $restricted, true)) {
            throw new JsonRpcSecurityException($message);
        }
    }

    private function actionLabel(string $methodName): string
    {
        return str_starts_with($methodName, 'delete') ? 'удалять' :
            (str_starts_with($methodName, 'create') ? 'создавать' :
            (str_starts_with($methodName, 'edit') ? 'редактировать' : 'использовать'));
    }
}
