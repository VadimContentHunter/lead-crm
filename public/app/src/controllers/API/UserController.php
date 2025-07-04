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
use crm\src\services\TableRenderer\TableRenderInput;
use crm\src\services\TableRenderer\TableTransformer;
use crm\src\services\TemplateRenderer\HeaderManager;
use crm\src\components\UserManagement\_entities\User;
use crm\src\components\UserManagement\UserManagement;
use crm\src\services\TemplateRenderer\TemplateRenderer;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;
use crm\src\services\TemplateRenderer\_common\TemplateBundle;
use crm\src\components\UserManagement\_common\mappers\UserInputMapper;
use crm\src\components\UserManagement\_common\mappers\UserFilterMapper;
use crm\src\components\UserManagement\_common\mappers\UserMapper;

class UserController
{
    private UserManagement $userManagement;

    private JsonRpcServerFacade $rpc;

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

        $this->rpc = new JsonRpcServerFacade();
        switch ($this->rpc->getMethod()) {
            case 'user.add':
                $this->createUser($this->rpc->getParams());
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
            is_string($params['login'] ?? null)
            && is_string($params['password'] ?? null)
            && is_string($params['password_confirm'] ?? null)
        ) {
            $userInputDto = UserInputMapper::fromArray($params);
            if ($userInputDto->plainPassword !== $userInputDto->confirmPassword) {
                $this->rpc->replyData([
                    ['type' => 'error', 'message' => 'Пароли не совпадают.']
                ]);
            }

            $executeResult = $this->userManagement->create()->execute($userInputDto);
            if ($executeResult->isSuccess()) {
                $login = $executeResult->getLogin() ?? 'неизвестный логин';
                $this->rpc->replyData([
                    ['type' => 'success', 'message' => 'Пользователь добавлен'],
                    ['type' => 'info', 'message' => "Добавленный пользователь: <b>{$login}</b>"]
                ]);
            } else {
                $errorMsg = $executeResult->getError()?->getMessage() ?? 'неизвестная ошибка';
                $this->rpc->replyData([
                    ['type' => 'error', 'message' => 'Пользователь не добавлен. Причина: ' . $errorMsg]
                ]);
            }
        } else {
            $this->rpc->replyData([
                    ['type' => 'error', 'message' => 'Данные пользователя некорректного формата.']
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
