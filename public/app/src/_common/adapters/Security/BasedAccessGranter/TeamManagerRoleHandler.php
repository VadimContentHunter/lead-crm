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
use crm\src\components\UserManagement\_common\adapters\UserResult;
use crm\src\components\LeadManagement\_common\DTOs\AccountManagerDto;
use crm\src\components\Security\_exceptions\JsonRpcSecurityException;
use crm\src\components\UserManagement\_common\interfaces\IUserResult;
use crm\src\components\UserManagement\_common\mappers\UserFilterMapper;
use crm\src\components\Security\_common\mappers\AccessFullContextMapper;
use crm\src\components\LeadManagement\_common\interfaces\ILeadRepository;
use crm\src\components\Security\_common\interfaces\IAccessRoleRepository;
use crm\src\components\UserManagement\_common\interfaces\IUserRepository;
use crm\src\components\Security\_common\interfaces\IAccessSpaceRepository;
use crm\src\_common\adapters\Security\BasedAccessGranter\IRoleAccessHandler;
use crm\src\components\Security\_common\interfaces\IAccessContextRepository;

class TeamManagerRoleHandler implements IRoleAccessHandler
{
    public function __construct(
        private IAccessContextRepository $contextRepository,
        private IAccessRoleRepository $roleRepository,
        private IAccessSpaceRepository $spaceRepository,
        private IUserRepository $userRepository,
        private ILeadRepository $leadRepository
    ) {
    }

    public function supports(object $target, string $methodName, AccessFullContextDTO $context): bool
    {
        return RoleNames::isTeamManager($context->role?->name ?? '');
    }

    /**
     * @return mixed
     */
    public function handle(AccessFullContextDTO $context, object $target, string $methodName, array $args): mixed
    {
        if ($target instanceof HandleAccessSpace && $methodName === 'getAllSpaces') {
            return $target->getAllSpaces('id', [$context->getSpaceId() ?? 0]);
        }

        if ($target instanceof HandleAccessRole && $methodName === 'getAllExceptRoles') {
            return $this->roleRepository->getAllByColumnValues('name', [
                        RoleNames::MANAGER->value,
                        RoleNames::TEAM_MANAGER->value
                    ]);
        }

        if ($target instanceof GetUser && $methodName === 'executeAllMapped') {
            $contexts = $this->contextRepository->getAllByColumnValues('space_id', [$context->getSpaceId() ?? 0]);
            $userIds = array_map(fn($c) => $c->userId, $contexts);
            $result = $this->userRepository->getAllByColumnValues('id', $userIds);
            $result = array_map(fn($u) => UserMapper::toArray($u), $result);
            return UserResult::success($result);
        }

        if ($target instanceof GetUser && $methodName === 'filtered') {
            return $this->getUsersFilter($context, $args);
        }

        if ($target instanceof UserController) {
            return $this->handleUserController($context, $target, $methodName, $args);
        }

        if ($target instanceof UserPage && $methodName === 'showEditUserPage') {
            throw new SecurityException("Тим менеджер не может редактировать пользователей.");
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
            // Получаем все AccessContext текущего пространства
            $contexts = $this->contextRepository->getAllByColumnValues('space_id', [$context->getSpaceId() ?? 0]);
            $userIds = array_map(fn($c) => $c->userId, $contexts);

            // Из запроса
            $leadAccountManagerId = $args[0]['accountManagerId'] ?? null;

            if (!is_numeric($leadAccountManagerId) || !in_array((int)$leadAccountManagerId, $userIds, true)) {
                throw new JsonRpcSecurityException("Недостаточно прав: выбранный менеджер не входит в ваше пространство.");
            }

            return $target->$methodName(...$args);
        }


        if ($target instanceof LeadRepository && $methodName === 'getAll') {
            $contexts = $this->contextRepository->getAllByColumnValues('space_id', [$context->getSpaceId() ?? 0]);
            $userIds = array_map(fn($c) => $c->userId, $contexts);

            // Если есть контексты — получаем лиды всех пользователей этого пространства,
            // иначе — только текущего пользователя
            $managerIds = !empty($userIds) ? $userIds : [$context->userId];

            // Загружаем лиды по колонке account_manager_id
            $res = $target->getAllByColumnValues('account_manager_id', $managerIds);

            // Добавляем название пространства
            foreach ($res as $lead) {
                $lead->groupName = $context->getSpaceName();
            }

            return $res;
        }

        if ($target instanceof LeadRepository && $methodName === 'getFilteredLeads') {
            $filter = $args[0] instanceof LeadFilterDto ? $args[0] : new LeadFilterDto();
            // $filter->manager = (string)$context->userId;
            return $this->getFilteredLeads($target, $filter, AccessFullContextMapper::toAccessContext($context));
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
                $contexts = $this->contextRepository->getAllByColumnValues('space_id', [$context->getSpaceId() ?? 0]);
                $userIds = array_map(fn($c) => $c->userId, $contexts);
                if (in_array((int)$leadAccountManagerId, $userIds, true)) {
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
    public function getFilteredLeads(
        LeadRepository $leadRepository,
        LeadFilterDto $filter,
        AccessContext $accessContext
    ): array {
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
        LEFT JOIN balances ON balances.lead_id = leads.id
        WHERE 1 = 1
    SQL;

        if (!empty($filter->groupName)) {
            // Проверка что группа доступна текущему пользователю
            $contexts = $this->contextRepository->getAllBySpaceId($accessContext->spaceId ?? 0);
            $allowedSpaceNames = array_unique(
                array_map(fn($context) => $this->spaceRepository->getById($context->spaceId ?? 0)?->name, $contexts)
            );

            if (!in_array($filter->groupName, $allowedSpaceNames, true)) {
                throw new SecurityException("Недостаточно прав для поиска по пространству '{$filter->groupName}'");
            }

            $sql .= " AND access_spaces.name = :space";
            $params['space'] = $filter->groupName;
        } else {
            // Если нет groupName → ищем по spaceId менеджера
            $sql .= " AND access_spaces.id = :spaceId";
            $params['spaceId'] = $accessContext->spaceId ?? 0;
        }

        // Баланс фильтры
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
    public function getUsersFilter(
        AccessFullContextDTO $context,
        array $args
    ): IUserResult {
        $allowedContexts = $this->contextRepository->getAllBySpaceId($context->getSpaceId() ?? 0);
        $allowedUserIds = array_map(fn($c) => $c->userId, $allowedContexts);

        $params = $args[0] ?? [];
        $userFilterDto = $params instanceof UserFilterDto ? $params : UserFilterMapper::fromArray($args[0]);

        $argLogin = $userFilterDto?->login ?? '';
        $argSearch = $userFilterDto?->search ?? '';

        $login = $argLogin;
        $search = $argSearch === '' ? $login : $argSearch;

        if (!in_array($context->userId, $allowedUserIds, true)) {
            throw new JsonRpcSecurityException("Недостаточно прав для поиска пользователей вне вашего пространства.");
        }
        if (is_numeric($search) && in_array((int)$search, $allowedUserIds, true)) {
            $allowedUsers = $this->userRepository->getAllByColumnValues('id', [(int)$search]);
            return UserResult::success($allowedUsers);
        }

        if (is_string($search) && $search !== '') {
            $allowedUsers = $this->userRepository->getAllByColumnValues('login', [$search]);
            return UserResult::success($allowedUsers);
        }

        return UserResult::success([]);
    }

    /**
     * @param array<int,mixed> $args
     */
    private function handleUserController(AccessFullContextDTO $context, UserController $target, string $method, array $args): mixed
    {
        // if (in_array($method, ['deleteUser', 'deleteUserById', 'editUser'], true)) {
        //     throw new JsonRpcSecurityException("Менеджер не может {$this->actionLabel($method)} пользователей.");
        // }

        // if (in_array($method, ['filterUsers', 'filterUsersFormatTable'], true)) {
        //     $allowedContexts = $this->contextRepository->getAllBySpaceId($context->spaceId ?? 0);
        //     $allowedUserIds = array_map(fn($c) => $c->userId, $allowedContexts);

        //     $userLogin = $this->userRepository->getById($context->userId)?->login;
        //     $argSearch = $args[0]['search'] ?? '';

        //     $login = $userLogin ?? '--';
        //     $search = $argSearch === '' ? $login : $argSearch;

        //     $isAllowed = false;
        //     if (in_array($context->userId, $allowedUserIds, true)) {
        //         $isAllowed = true;
        //     }
        //     if (is_numeric($search) && in_array((int)$search, $allowedUserIds, true)) {
        //         $isAllowed = true;
        //     }
        //     if ($search === $login) {
        //         $isAllowed = true;
        //     }

        //     if (!$isAllowed) {
        //         throw new JsonRpcSecurityException("Недостаточно прав для поиска пользователей вне вашего пространства.");
        //     }

        //     // Получаем всех пользователей по разрешённым userId
        //     $allowedUsers = $this->userRepository->getAllByColumnValues('id', $allowedUserIds);

        //     // Например, преобразуем их в массивы для вывода
        //     $result = array_map(fn($user) => UserMapper::toArray($user), $allowedUsers);

        //     // Передаём результат в фильтр
        //     $target->filterUsersFormatTable(['users' => $result, 'login' => $login, 'search' => $search]);
        //     // Возврата не требуется
        // }


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
                throw new JsonRpcSecurityException("Тим-Менеджер может добавлять только в свое пространство.");
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
