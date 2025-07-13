<?php

namespace crm\src\controllers;

use PDO;
use Throwable;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\services\AppContext\AppContext;
use crm\src\services\AppContext\IAppContext;
use crm\src\services\TableRenderer\TableFacade;
use crm\src\_common\repositories\SourceRepository;
use crm\src\services\TableRenderer\TableDecorator;
use crm\src\_common\adapters\SourceValidatorAdapter;
use crm\src\services\TableRenderer\TableRenderInput;
use crm\src\services\TableRenderer\TableTransformer;
use crm\src\services\TemplateRenderer\HeaderManager;
use crm\src\components\UserManagement\_entities\User;
use crm\src\services\TemplateRenderer\TemplateRenderer;
use crm\src\components\SourceManagement\_entities\Source;
use crm\src\components\SourceManagement\SourceManagement;
use crm\src\services\TemplateRenderer\_common\TemplateBundle;
use crm\src\components\SourceManagement\_common\interfaces\ISourceResult;

class SourcePage
{
    public SourceManagement $sourceManagement;

    private TemplateRenderer $renderer;

    public function __construct(
        private IAppContext $appContext
    ) {
        $this->renderer = $this->appContext->getTemplateRenderer();
        $this->sourceManagement = $this->appContext->getSourceManagement();
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

    public function showAddSourcePage(): void
    {
        $this->showPage([
            'components' => [(new TemplateBundle(templatePath: 'components/addSource.tpl.php'))]
        ]);
    }

    public function getRenderTable(): string
    {
        $headers = $this->sourceManagement->get()->executeColumnNames()->getArray();
        $rows = $this->sourceManagement->get()->executeAllMapped(function (Source $source) {
            return [
                'id' => $source->id,
                'title' => $source->title,
            ];
        })->getArray();

        $input = new TableRenderInput(
            header: $headers,
            rows: $rows,
            attributes: ['id' => 'source-table-1', 'data-module' => 'sources'],
            classes: ['base-table'],
            hrefButton: '/page/source-edit',
            hrefButtonDel: '/page/source-delete',
            attributesWrapper: [
                'table-r-id' => 'source-table-1'
            ],
            allowedColumns: [
                'id',
                'title',
            ],
            renameMap: [],
        );

        $tableFacade = new TableFacade(new TableTransformer(),  new TableDecorator());
        return $tableFacade->renderTable($input)->asHtml();
    }

    public function getTableSourceComponent(): TemplateBundle
    {
        return (new TemplateBundle(
            templatePath: 'containers/average-in-line-component.tpl.php',
            variables: [
                'component' => $this->getRenderTable(),
                'methodSend' => 'source.delete',
                'endpointSend' => '/api/sources'
            ]
        ));
    }

    public function showAllSourcePage(): void
    {
        $this->showPage([
            'components' => [
                $this->getTableSourceComponent()
            ]
        ]);
    }
}
