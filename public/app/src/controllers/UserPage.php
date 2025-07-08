<?php

namespace crm\src\controllers;

use PDO;
use Throwable;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\components\Security\RoleNames;
use crm\src\controllers\NotFoundController;
use crm\src\services\AppContext\IAppContext;
use crm\src\services\TableRenderer\TableFacade;
use crm\src\_common\repositories\UserRepository;
use crm\src\_common\adapters\UserValidatorAdapter;
use crm\src\services\TableRenderer\TableDecorator;
use crm\src\services\TableRenderer\TableRenderInput;
use crm\src\services\TableRenderer\TableTransformer;
use crm\src\services\TemplateRenderer\HeaderManager;
use crm\src\components\Security\_entities\AccessRole;
use crm\src\components\UserManagement\_entities\User;
use crm\src\components\UserManagement\UserManagement;
use crm\src\components\Security\_entities\AccessSpace;
use crm\src\services\TemplateRenderer\TemplateRenderer;
use crm\src\services\TemplateRenderer\_common\TemplateBundle;
use crm\src\components\Security\_common\interfaces\IHandleAccessRole;
use crm\src\components\Security\_common\interfaces\IHandleAccessSpace;
use crm\src\components\UserManagement\_common\interfaces\IUserManagement;

class UserPage
{
    private IUserManagement $userManagement;

    // private HandleAccessRole $handleAccessRole;
    private IHandleAccessSpace $handleAccessSpace;

    private IHandleAccessRole $handleAccessRole;

    // private SecureWrapper $handleAccessSpace;

    private TemplateRenderer $renderer;

    public function __construct(
        private IAppContext $appContext,
    ) {
        $this->renderer = $appContext->getTemplateRenderer();
        $this->userManagement = $appContext->getUserManagement();
        $this->handleAccessSpace = $appContext->getHandleAccessSpace();
        $this->handleAccessRole = $appContext->getHandleAccessRole();
    }

    /**
     * @param array<string, mixed> $components
     */
    public function showPage(array $components): void
    {
        $headers = new HeaderManager();
        $headers->set('Content-Type', 'text/html; charset=utf-8');
        $this->renderer->setHeaders($headers);

        try {
            // Успешный ответ
            $headers->setResponseCode(200);
            echo $this->renderer->renderBundle($this->appContext?->getLayout($components));
        } catch (Throwable $e) {
            // Внутренняя ошибка — HTTP 500
            $headers->setResponseCode(500);
            // header('Content-Type: text/plain; charset=utf-8');
            // echo "Произошла ошибка: " . $e->getMessage();
            throw $e;
        }
    }

    public function showAddUserPage(): void
    {
        $spaces = $this->handleAccessSpace->getAllSpaces();
        $roles = $this->handleAccessRole->getAllExceptRoles('name', [RoleNames::SUPER_ADMIN->value]);

        if (count($roles) > 1) {
            array_unshift($roles, new AccessRole("По умолчанию", null, "По умолчанию"));
        }

        array_unshift($spaces, new AccessSpace("По умолчанию", null, "По умолчанию"));
        $this->showPage([
            'components' => [(new TemplateBundle(
                templatePath: 'components/addUser.tpl.php',
                variables: [
                    'roles' => $roles,
                    'spaces' => $spaces,
                ]
            ))]
        ]);
    }

    public function showAllUserPage(): void
    {
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

        $this->showPage([
            'components' => [
                // (new TemplateBundle(
                //     templatePath: 'components/tableUsers.tpl.php',
                //     variables: [
                //         'header' => $tableFacade->getRenderHeaderRows($input)?->header ?? [],
                //         'rows' => $tableFacade->getRenderHeaderRows($input)?->rows ?? [],
                //     ]
                // )),


                (new TemplateBundle(
                    templatePath: 'containers/average-in-line-component.tpl.php',
                    variables: [
                        'component' => $tableFacade->renderFilteredTable($input)->asHtml(),
                        'filterPanel' => (new TemplateBundle(
                            templatePath: 'partials/filtersUser.tpl.php',
                            variables: [
                                'sortColumns' => $headers,
                                'selectedData' => [],
                            ]
                        )),
                        'methodSend' => 'user.delete',
                        'endpointSend' => '/api/users'
                    ]
                ))
            ]
        ]);
    }

    public function showEditUserPage(string|int $userId): void
    {
        if (!filter_var($userId, FILTER_VALIDATE_INT)) {
            (new NotFoundController())->show404();
            exit();
        }

        $result = $this->userManagement->get()->executeById((int)$userId);
        if (!$result->isSuccess()) {
            (new NotFoundController())->show404();
            exit();
        }

        $this->showPage([
            'components' => [(new TemplateBundle(
                templatePath: 'components/editUser.tpl.php',
                variables: [
                    'login' => $result->getLogin(),
                    'userId' => $userId
                ]
            ))]
        ]);
    }
}
