<?php

namespace crm\src\controllers;

use Throwable;
use crm\src\services\AppContext\IAppContext;
use crm\src\services\TableRenderer\TableFacade;
use crm\src\services\TableRenderer\TableDecorator;
use crm\src\services\TableRenderer\TableRenderInput;
use crm\src\services\TableRenderer\TableTransformer;
use crm\src\services\TemplateRenderer\HeaderManager;
use crm\src\components\UserManagement\_entities\User;
use crm\src\services\TemplateRenderer\TemplateRenderer;
use crm\src\services\TemplateRenderer\_common\TemplateBundle;
use crm\src\components\Security\_common\interfaces\IHandleAccessRole;
use crm\src\components\Security\_common\interfaces\IHandleAccessSpace;
use crm\src\components\UserManagement\_common\interfaces\IUserManagement;

class TestController
{
    private IUserManagement $userManagement;

    private TemplateRenderer $renderer;

    private HeaderManager $headers;

    public function __construct(
        private IAppContext $appContext
    ) {
        $this->headers = new HeaderManager();
        $this->headers->set('Content-Type', 'text/html; charset=utf-8');

        $this->renderer = $this->appContext->getTemplateRenderer();
        $this->renderer->setHeaders($this->headers);

        $this->userManagement = $appContext->getUserManagement();

        $this->showPage(['components' => [
            (new TemplateBundle(
                templatePath: 'test.tpl.php',
            )),
            (new TemplateBundle(
                templatePath: 'containers/component-wrapper-line.tpl.php',
                variables: [
                    'component' => (new TemplateBundle(
                        templatePath: 'components/addUser.tpl.php',
                        variables: [
                        'roles' => [],
                        'spaces' => [],
                        ]
                    )),
                ]
            )),
            $this->getTable()
        ]]);
    }

    /**
     * @param array<string, mixed> $components
     */
    public function showPage(array $components): void
    {
         // $renderer = new TemplateRenderer(baseTemplateDir: $this->projectPath . '/src/templates/');
        // $layout = (new TemplateBundle(templatePath: 'components/addUser.tpl.php'));
        $headers = new HeaderManager();
        $headers->set('Content-Type', 'text/html; charset=utf-8');
        $this->renderer->setHeaders($headers);

        try {
            $headers->setResponseCode(200);
            echo $this->renderer->renderBundle($this->appContext->getLayout($components));
        } catch (Throwable $e) {
            // Внутренняя ошибка — HTTP 500
            $headers->setResponseCode(500);
            // header('Content-Type: text/plain; charset=utf-8');
            // echo "Произошла ошибка: " . $e->getMessage();
            throw $e;
        }
    }

    public function getTable(): TemplateBundle
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
            classesWrapper: ['table-wrapper'],
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
        return (new TemplateBundle(
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
        ));
    }
}
