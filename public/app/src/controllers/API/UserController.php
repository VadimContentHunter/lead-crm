<?php

namespace crm\src\controllers\API;

use PDO;
use Throwable;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\services\TableRenderer\TableFacade;
use crm\src\_common\repositories\UserRepository;
use crm\src\_common\adapters\UserValidatorAdapter;
use crm\src\services\TableRenderer\TableDecorator;
use crm\src\components\Security\SessionAuthManager;
use crm\src\services\TableRenderer\TableRenderInput;
use crm\src\services\TableRenderer\TableTransformer;
use crm\src\components\Security\_entities\AccessRole;
use crm\src\components\UserManagement\_entities\User;
use crm\src\components\UserManagement\UserManagement;
use crm\src\_common\repositories\AccessRoleRepository;
use crm\src\_common\repositories\AccessSpaceRepository;
use crm\src\_common\repositories\AccessContextRepository;
use crm\src\components\Security\_handlers\HandleAccessRole;
use crm\src\components\Security\_handlers\HandleAccessSpace;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;
use crm\src\components\Security\_handlers\HandleAccessContext;
use crm\src\components\UserManagement\_common\mappers\UserMapper;
use crm\src\components\UserManagement\_common\mappers\UserEditMapper;
use crm\src\components\UserManagement\_common\mappers\UserInputMapper;
use crm\src\components\UserManagement\_common\mappers\UserFilterMapper;
use DateTime;

class UserController
{
    private UserManagement $userManagement;

    private JsonRpcServerFacade $rpc;

    private SessionAuthManager $sessionAuthManager;

    private HandleAccessContext $handleAccessContext;

    private HandleAccessRole $handleAccessRole;

    private HandleAccessSpace $handleAccessSpace;

    public function __construct(
        private string $projectPath,
        PDO $pdo,
        private LoggerInterface $logger = new NullLogger()
    ) {
        $this->logger->info('UserController initialized for project ' . $this->projectPath);
        $this->userManagement = new UserManagement(
            new UserRepository($pdo, $logger),
            new UserValidatorAdapter()
        );

        $accessContextRepository = new AccessContextRepository($pdo, $this->logger);
        $this->sessionAuthManager = new SessionAuthManager($accessContextRepository);
        $this->handleAccessContext = new HandleAccessContext($accessContextRepository);
        $this->handleAccessRole = new HandleAccessRole(new AccessRoleRepository($pdo, $this->logger));
        $this->handleAccessSpace = new HandleAccessSpace(new AccessSpaceRepository($pdo, $this->logger));


        $this->rpc = new JsonRpcServerFacade();
        switch ($this->rpc->getMethod()) {
            case 'user.add':
                $this->createUser($this->rpc->getParams());
            // break;

            case 'user.edit':
                $this->editUser($this->rpc->getParams());
            // break;

            case 'user.delete':
                $this->deleteUser($this->rpc->getParams());
            // break;

            case 'user.filter':
                $this->filterUsers($this->rpc->getParams());
            // break;

            case 'user.filter.table':
                $this->filterUsersFormatTable($this->rpc->getParams());
            // break;

            case 'user.filter.table.clear':
                $this->filterUsersFormatTable([]);
            // break;

            default:
                $this->rpc->replyError(-32601, 'Метод не найден');
        }
    }

    /**
     * @param array<string,mixed> $params
     */
    public function createUser(array $params): void
    {
        if (
            !is_string($params['login'] ?? null)
            || !is_string($params['password'] ?? null)
            || !is_string($params['password_confirm'] ?? null)
        ) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Данные пользователя некорректного формата.']
            ]);
        }

        $role = null;
        $space = null;

        if (!isset($params['role_id']) || filter_var($params['role_id'], FILTER_VALIDATE_INT) === false) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Роль не выбрана.']
            ]);
        }

        $role = $this->handleAccessRole->getRoleById($params['role_id']);
        if ($role === null) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Не существует такой роли.']
            ]);
        }

        if ($role->name === "superadmin") {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Данная роль не предназначена для создания пользователей.']
            ]);
        }

        if ($role->name === "manager") {
            if (isset($params['space_id']) && filter_var($params['space_id'], FILTER_VALIDATE_INT) === false) {
                $this->rpc->replyData([
                    ['type' => 'error', 'message' => 'Для этой роли должно быть выбрано пространство.']
                ]);
            }

            if (isset($params['space_id'])) {
                $space = $this->handleAccessSpace->getSpaceById($params['space_id']);
                if ($space === null) {
                    $this->rpc->replyData([
                        ['type' => 'error', 'message' => 'Пространство не выбрано.']
                    ]);
                }
            }
        }


        $userInputDto = UserInputMapper::fromArray($params);
        if ($userInputDto->plainPassword !== $userInputDto->confirmPassword) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Пароли не совпадают.']
            ]);
        }

        $user = $this->userManagement->create()->execute($userInputDto);
        if (!$user->isSuccess()) {
            $errorMsg = $user->getError()?->getMessage() ?? 'неизвестная ошибка';
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Пользователь не добавлен. Причина: ' . $errorMsg]
            ]);
        }
                // $this->sessionAuthManager
                // $this->handleAccessContext

        $sessionHash = $this->handleAccessContext->generateSessionHash(
            $user->getLogin() ?? '',
            $user->getPasswordHash() ?? ''
        );
        if ($sessionHash === null) {
            $this->deleteUserById($user->getId() ?? 0);
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Не удалось создать сессию.']
            ]);
        }

        if ($role->name === "team-manager") {
            $spaceName = ($user->getLogin() ?? '') . '_space';
            $spaceDescription = (new DateTime())->format('Y-m-d H:i:s');
            $space = $this->handleAccessSpace->addSpace($spaceName, $spaceDescription);
            if ($space === null) {
                $this->handleAccessContext->delAccessById($accessContext->id ?? 0);
                $this->deleteUserById($user->getId() ?? 0);
                $this->rpc->replyData([
                    ['type' => 'error', 'message' => 'Не удалось выдать доступ пользователю. (3)']
                ]);
            }
        }

        $accessContext  = $this->handleAccessContext->createAccess(
            $user->getId() ?? 0,
            $sessionHash,
            $role->id ?? 0,
            $space?->id ?? 0
        );
        if ($accessContext === null) {
            $this->deleteUserById($user->getId() ?? 0);
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Не удалось выдать доступ пользователю. (1)']
            ]);
        }

        if ($this->handleAccessRole->getRoleById($role->id ?? 0) === null) {
            $this->handleAccessContext->delAccessById($accessContext->id ?? 0);
            $this->deleteUserById($user->getId() ?? 0);
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Не удалось выдать доступ пользователю. (2)']
            ]);
        }

        // $this->sessionAuthManager->login($sessionHash);
        $login = $user->getLogin() ?? 'неизвестный логин';
        $this->rpc->replyData([
            ['type' => 'success', 'message' => 'Пользователь добавлен'],
            ['type' => 'info', 'message' => "Добавленный пользователь: <b>{$login}</b>"]
        ]);
    }

    private function deleteUserById(int $id): void
    {
        $resDelete = $this->userManagement->delete()->executeById($id);
        if (!$resDelete->isSuccess()) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Не удалось удалить пользователя.']
            ]);
        }
    }

    /**
     * @param array<string,mixed> $params
     */
    public function editUser(array $params): void
    {
        $id = $params['userId'] ?? $params['id'] ?? null;
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'ID User должен быть целым числом.']
            ]);
        }

        $params['id'] = (int)$id;
        if (is_string($params['login'] ?? null)) {
            $userEditDto = UserInputMapper::fromArray($params);

            if (
                $userEditDto->plainPassword !== ''
                && $userEditDto->plainPassword !== $userEditDto->confirmPassword
            ) {
                $this->rpc->replyData([
                    ['type' => 'error', 'message' => 'Пароли не совпадают.']
                ]);
            }

            $executeResult = $this->userManagement->update()->execute($userEditDto);
            if ($executeResult->isSuccess()) {
                $login = $executeResult->getLogin() ?? 'неизвестный логин';
                $this->rpc->replyData([
                    ['type' => 'success', 'message' => 'Пользователь обновлен'],
                    ['type' => 'info', 'message' => "Обновлённый пользователь: <b>{$login}</b>"]
                ]);
            } else {
                $errorMsg = $executeResult->getError()?->getMessage() ?? 'неизвестная ошибка';
                $this->rpc->replyData([
                    ['type' => 'error', 'message' => 'Пользователь не обновлен. Причина: ' . $errorMsg]
                ]);
            }
        } else {
            $this->rpc->replyData([
                    ['type' => 'error', 'message' => 'Данные пользователя некорректного формата.']
                ]);
        }
    }

    /**
     * @param array<string,mixed> $params
     */
    public function deleteUser(array $params): void
    {
        $id = $params['row_id'] ?? $params['id'] ?? null;
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'ID User должен быть целым числом.']
            ]);
        }

        $executeResult = $this->userManagement->delete()->executeById((int)$id);
        if ($executeResult->isSuccess()) {
            $this->filterUsersFormatTable([]);
        } else {
            $errorMsg = $executeResult->getError()?->getMessage() ?? 'неизвестная ошибка';
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Пользователь не удалён. Причина: ' . $errorMsg]
            ]);
        }
    }

    /**
     * @param array<string, mixed> $params
     */
    public function filterUsers(array $params): void
    {
        $executeResult = $this->userManagement->get()->filtered(UserFilterMapper::fromArray($params));
        if ($executeResult->isSuccess()) {
            $this->rpc->replyData([
                ['type' => 'success', 'leads' => $executeResult->getArray()]
            ]);
        } else {
            $errorMsg = $executeResult->getError()?->getMessage() ?? 'неизвестная ошибка';
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Ошибка при фильтрации. Причина: ' . $errorMsg]
            ]);
        }
    }

    /**
     * @param array<string, mixed> $params
     */
    public function filterUsersFormatTable(array $params): void
    {
        $executeResult = $this->userManagement->get()->filtered(UserFilterMapper::fromArray($params));
        if ($executeResult->isSuccess()) {
            $headers = $this->userManagement->get()->executeColumnNames()->getArray();
            $rows = $executeResult->mapEach(function (User|array $user) {
                $userData = is_object($user) ? UserMapper::toArray($user) : $user;
                return [
                    'id' => $userData['id'],
                    'login' => $userData['login'],
                    'password_hash' => '',
                ];
            })->getArray();

            $input = new TableRenderInput(
                header: $headers,
                rows: $rows,
                attributes: ['id' => 'user-table-1', 'data-module' => 'users'],
                classes: ['base-table'],
                hrefButton: '/page/user-edit',
                hrefButtonDel: '/page/user-delete',
                attributesWrapper: [
                    'table-r-id' => 'user-table-1'
                ],
                allowedColumns: [
                    'id',
                    'login',
                    'password_hash',
                ],
                renameMap: [
                    'password_hash' => 'Пароль',
                ],
            );

            $tableFacade = new TableFacade(new TableTransformer(),  new TableDecorator());
            $this->rpc->replyData([
                'type' => 'success',
                'table' => $tableFacade->renderFilteredTable($input)->asHtml()
            ]);
        } else {
            $errorMsg = $executeResult->getError()?->getMessage() ?? 'неизвестная ошибка';
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Ошибка при фильтрации. Причина: ' . $errorMsg]
            ]);
        }
    }
}
