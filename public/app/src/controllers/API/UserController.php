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

class UserController
{
    private UserManagement $userManagement;

    private JsonRpcServerFacade $rpc;

    public function __construct(
        private string $projectPath,
        PDO $pdo,
        private LoggerInterface $logger = new NullLogger()
    ) {
        $this->userManagement = new UserManagement(
            new UserRepository($pdo, $logger),
            new UserValidatorAdapter()
        );

        $this->rpc = new JsonRpcServerFacade();
        switch ($this->rpc->getMethod()) {
            case 'user.add':
                $this->createUser($this->rpc->getParams());
            // break;

            case 'user.show.add_page':
                $this->showAddUserPage();
            // break;

            case 'user.show.all_page':
                $this->showAllUserPage();
            // break;

            default:
                $this->rpc->replyError(-32601, 'Метод не найден');
        }
    }

    public function showAddUserPage(): void
    {
        $renderer = new TemplateRenderer(baseTemplateDir: $this->projectPath . '/src/templates/');
        $layout = (new TemplateBundle(templatePath: 'components/addUser.tpl.php'));
        try {
            $this->rpc->replyContentUpdate('main.main-content', $renderer->renderBundle($layout));
        } catch (Throwable $e) {
            $this->rpc->replyError(-32601, 'Не удалось сгенерировать страницу для добавления пользователя');
            // header('Content-Type: text/plain; charset=utf-8');
            // echo "Произошла ошибка: " . $e->getMessage();
            throw $e;
        }
    }

    public function showAllUserPage(): void
    {
        // $rowsTable = $this->userManagement->get()->executeAsTable(true)->getData();
        // $headerTable = array_shift($rowsTable); // первая строка — заголовок

        // $transformer = new UserTableTransformer();
        // $decorator = new UserTableDecorator();

        // $transformedTable = $transformer->transform($headerTable, $rowsTable);
        // $tableWithActions = $decorator->decorateWithActions($headerTable, $transformedTable);

        // $renderer = new TemplateRenderer(baseTemplateDir: $this->projectPath . '/src/templates/');
        // $layout = (new TemplateBundle(
        //     templatePath: 'components/baseTable.tpl.php',
        //     variables: [
        //         // 'columns' => ['Название', 'Тип', 'Значение', 'Опции'],
        //         'columns' => $tableWithActions['header'] ?? [],
        //         'rows' => $tableWithActions['rows'] ?? [],
        //     ]
        // ));

        $headers = $this->userManagement->get()->executeColumnNames()->getArray();
        $rows = $this->userManagement->get()->executeAllMapped(function (User $user) {
            return [
                'id' => $user->id,
                'login' => $user->login,
                'password_hash' => '',
            ];
        })->getArray();

        $input = new TableRenderInput(
            header: $headers,
            rows: $rows,
            attributes: ['id' => 'user-table-1', 'data-module' => 'users'],
            classes: ['base-table']
        );

        $tableFacade = new TableFacade(new TableTransformer(),  new TableDecorator());
        $resultTable = $tableFacade->renderTable($input);

        $renderer = new TemplateRenderer(baseTemplateDir: $this->projectPath . '/src/templates/');
        $layout = (new TemplateBundle(
            templatePath: 'containers/average-in-line-component.tpl.php',
            variables: [
                'component' => $resultTable->asHtml()
            ]
        ));

        try {
            $this->rpc->replyContentUpdate('main.main-content', $renderer->renderBundle($layout));
        } catch (Throwable $e) {
            $this->rpc->replyError(-32601, 'Не удалось сгенерировать страницу для добавления пользователя');
            // header('Content-Type: text/plain; charset=utf-8');
            // echo "Произошла ошибка: " . $e->getMessage();
            throw $e;
        }
    }

    /**
     * @return TemplateBundle[]
     */
    public function getComponentsForPage(): array
    {
        return [
            (new TemplateBundle(templatePath: 'components/addUser.tpl.php')),
        ];
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
}
