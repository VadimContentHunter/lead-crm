<?php

namespace crm\src\controllers;

use PDO;
use Throwable;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\services\Partials;
use crm\src\services\TableRenderer\TableFacade;
use crm\src\_common\repositories\StatusRepository;
use crm\src\services\TableRenderer\TableDecorator;
use crm\src\_common\adapters\StatusValidatorAdapter;
use crm\src\services\TableRenderer\TableRenderInput;
use crm\src\services\TableRenderer\TableTransformer;
use crm\src\services\TemplateRenderer\HeaderManager;
use crm\src\components\Security\_entities\AccessRole;
use crm\src\components\UserManagement\_entities\User;
use crm\src\components\Security\_entities\AccessSpace;
use crm\src\services\TemplateRenderer\TemplateRenderer;
use crm\src\components\StatusManagement\_entities\Status;
use crm\src\components\StatusManagement\StatusManagement;
use crm\src\services\TemplateRenderer\_common\TemplateBundle;

class StatusPage
{
    private StatusManagement $statusManagement;

    private TemplateRenderer $renderer;

    public function __construct(
        private string $projectPath,
        PDO $pdo,
        private LoggerInterface $logger = new NullLogger(),
        private ?Partials $partials = null
    ) {
        $this->logger->info('StatusPage initialized');
        $this->renderer = new TemplateRenderer(baseTemplateDir: $this->projectPath . '/src/templates/');
        $this->statusManagement = new StatusManagement(
            new StatusRepository($pdo, $logger),
            new StatusValidatorAdapter()
        );
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
            echo $this->renderer->renderBundle($this->partials->getLayout($components));
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

    public function showAllStatusPage(): void
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
            classes: ['base-table']
        );

        $tableFacade = new TableFacade(new TableTransformer(),  new TableDecorator());

        $this->showPage([
            'components' => [
                (new TemplateBundle(
                    templatePath: 'containers/average-in-line-component.tpl.php',
                    variables: [
                        'component' => $tableFacade->renderTable($input)->asHtml()
                    ]
                ))
            ]
        ]);
    }
}
