<?php

namespace crm\src\controllers\API;

use PDO;
use DateTime;
use Throwable;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\components\Security\RoleNames;
use crm\src\services\AppContext\ISecurity;
use crm\src\services\AppContext\IAppContext;
use crm\src\components\Security\SecureWrapper;
use crm\src\services\TableRenderer\TableFacade;
use crm\src\_common\repositories\UserRepository;
use crm\src\_common\adapters\UserValidatorAdapter;
use crm\src\services\TableRenderer\TableDecorator;
use crm\src\components\Security\SessionAuthManager;
use crm\src\services\AppContext\SecurityAppContext;
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
use crm\src\components\Security\_common\interfaces\IHandleAccessRole;
use crm\src\components\Security\_exceptions\JsonRpcSecurityException;
use crm\src\components\UserManagement\_common\mappers\UserEditMapper;
use crm\src\components\Security\_common\interfaces\IHandleAccessSpace;
use crm\src\components\UserManagement\_common\mappers\UserInputMapper;
use crm\src\components\UserManagement\_common\mappers\UserFilterMapper;
use crm\src\components\UserManagement\_common\interfaces\IUserManagement;

class UserController
{
    private IUserManagement $userManagement;

    private JsonRpcServerFacade $rpc;

    private HandleAccessContext $handleAccessContext;

    private IHandleAccessRole $handleAccessRole;

    private IHandleAccessSpace $handleAccessSpace;

    /**
     * @var array<string, callable>
     */
    private array $methods = [];

    public function __construct(
        private IAppContext $appContext,
    ) {
        $this->userManagement = $this->appContext->getUserManagement();
        $this->handleAccessContext = $this->appContext->getHandleAccessContext();
        $this->handleAccessRole = $this->appContext->getHandleAccessRole();
        $this->handleAccessSpace = $this->appContext->getHandleAccessSpace();
        $this->rpc = $this->appContext->getJsonRpcServerFacade();

        $this->initMethodMap();
        $this->init();
    }

    private function initMethodMap(): void
    {
        if ($this->appContext instanceof ISecurity) {
            /**
             * @var UserController $secureCall
             */
            $secureCall = $this->appContext->wrapWithSecurity($this);
        } else {
            $secureCall = $this;
        }

        $this->methods = [
            'user.add'                => fn() => $secureCall->createUser($this->rpc->getParams()),
            'user.edit'               => fn() => $secureCall->editUser($this->rpc->getParams()),
            'user.delete'             => fn() => $secureCall->deleteUser($this->rpc->getParams()),
            'user.filter'             => fn() => $secureCall->filterUsers($this->rpc->getParams()),
            'user.filter.table'       => fn() => $secureCall->filterUsersFormatTable($this->rpc->getParams()),
            'user.filter.table.clear' => fn() => $secureCall->filterUsersFormatTable([]),
        ];
    }

    public function init(): void
    {
        try {
            $method = $this->rpc->getMethod();

            if (!isset($this->methods[$method])) {
                throw new JsonRpcSecurityException('Метод не найден', -32601);
            }

            ($this->methods[$method])();
        } catch (JsonRpcSecurityException $e) {
            $this->rpc->send($e->toJsonRpcError($this->rpc->getId()));
        } catch (\Throwable $e) {
            $this->rpc->replyError(-32000, $e->getMessage());
        }
    }


    /**
     * @param array<string, mixed> $params
     */
    public function createUser(array $params): void
    {
        // 1. Проверка базовых параметров пользователя
        if (
            !is_string($params['login'] ?? null)
            || !is_string($params['password'] ?? null)
            || !is_string($params['password_confirm'] ?? null)
        ) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Данные пользователя некорректного формата.']
            ]);
        }

        // 2. Проверка роли пользователя
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

        $space = $this->handleAccessSpace->getSpaceById($params['space_id'] ?? 0);
        if ($space === null && $params['space_id'] !== null) {
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Пространство не выбрано.']
            ]);
        }

        // 5. Создание DTO пользователя и проверка пароля
        $userInputDto = UserInputMapper::fromArray($params);
        if ($userInputDto->plainPassword !== $userInputDto->confirmPassword) {
            $this->rpc->replyData([
            ['type' => 'error', 'message' => 'Пароли не совпадают.']
            ]);
        }

        // 6. Создание пользователя
        $user = $this->userManagement->create()->execute($userInputDto);
        if (!$user->isSuccess()) {
            $errorMsg = $user->getError()?->getMessage() ?? 'неизвестная ошибка';
            $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Пользователь не добавлен. Причина: ' . $errorMsg]
            ]);
        }

        // 7. Генерация сессии
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

        // 8. Автоматическое создание пространства для роли TeamManager
        if (RoleNames::isTeamManager($role->name)) {
            $spaceName = ($user->getLogin() ?? '') . '_space';
            $spaceDescription = (new DateTime())->format('Y-m-d H:i:s');
            $space = $this->handleAccessSpace->addSpace($spaceName, $spaceDescription);
            if ($space === null) {
                $this->deleteUserById($user->getId() ?? 0);
                $this->rpc->replyData([
                ['type' => 'error', 'message' => 'Не удалось выдать доступ пользователю. (3)']
                ]);
            }
        }

        // 9. Создание записи доступа (AccessContext)
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

        // 10. Финальная проверка корректности доступа
        if ($this->handleAccessRole->getRoleById($role->id ?? 0) === null) {
            $this->handleAccessContext->delAccessById($accessContext->id ?? 0);
            $this->deleteUserById($user->getId() ?? 0);
            $this->rpc->replyData([
            ['type' => 'error', 'message' => 'Не удалось выдать доступ пользователю. (2)']
            ]);
        }

        // 11. Возвращение успешного ответа
        $login = $user->getLogin() ?? 'неизвестный логин';
        $roleName = $role->name ?? 'нет';
        $spaceName = $space?->name ?? 'нет';
        $this->rpc->replyData([
        ['type' => 'success', 'message' => 'Пользователь добавлен'],
            ['type' => 'info', 'message' => <<<HTML
                    Добавленный пользователь:
                    <br>Логин: <b>{$login}</b>
                    <br>Роль: <b>{$roleName}</b>
                    <br>Пространство: <b>{$spaceName}</b>
                HTML
            ]
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
        $id = $params['row_id'] ?? $params['rowId'] ?? $params['id'] ?? null;
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
                classesWrapper: ['table-wrapper'],
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
