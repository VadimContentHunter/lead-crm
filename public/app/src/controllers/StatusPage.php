<?php

namespace crm\src\controllers;

use Throwable;
use crm\src\services\AppContext\IAppContext;
use crm\src\services\TableRenderer\TableFacade;
use crm\src\services\TableRenderer\TableDecorator;
use crm\src\services\TableRenderer\TableRenderInput;
use crm\src\services\TableRenderer\TableTransformer;
use crm\src\services\TemplateRenderer\HeaderManager;
use crm\src\services\TemplateRenderer\TemplateRenderer;
use crm\src\components\StatusManagement\_entities\Status;
use crm\src\components\StatusManagement\StatusManagement;
use crm\src\services\TemplateRenderer\_common\TemplateBundle;
use crm\src\components\StatusManagement\_common\interfaces\IStatusManagement;

class StatusPage
{
    private IStatusManagement $statusManagement;

    private TemplateRenderer $renderer;

    public function __construct(
        private IAppContext $appContext
    ) {
        $this->statusManagement = $appContext->getStatusManagement();
        $this->renderer = $appContext->getTemplateRenderer();
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
            // Успешный ответ
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

    public function showAddStatusPage(): void
    {
        $this->showPage([
            'components' => [(new TemplateBundle(templatePath: 'components/addStatus.tpl.php'))]
        ]);
    }

    public function getTableStatusComponent(): TemplateBundle
    {
        $headers = $this->statusManagement->get()->executeColumnNames()->getArray();
        $rows = $this->statusManagement->get()->executeAllMapped(function (Status $status) {
            return [
                'id' => $status->id,
                'title' => $status->title,
            ];
        })->getArray();

        $input = new TableRenderInput(
            header: $headers,
            rows: $rows,
            attributes: ['id' => 'status-table-1', 'data-module' => 'statuses'],
            classes: ['base-table'],
            hrefButton: '/page/status-edit',
            hrefButtonDel: '/page/status-delete',
            attributesWrapper: [
                'table-r-id' => 'status-table-1'
            ],
            allowedColumns: [
                'id',
                'title',
            ],
            renameMap: [],
        );

        $tableFacade = new TableFacade(new TableTransformer(),  new TableDecorator());
        return (new TemplateBundle(
            templatePath: 'containers/average-in-line-component.tpl.php',
            variables: [
                'component' => $tableFacade->renderTable($input)->asHtml(),
                'methodSend' => 'status.delete',
                'endpointSend' => '/api/statuses'
            ]
        ));
    }

    public function showAllStatusPage(): void
    {
        $this->showPage([
            'components' => [
                $this->getTableStatusComponent()
            ]
        ]);
    }
}
