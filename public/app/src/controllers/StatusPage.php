<?php

namespace crm\src\controllers;

use PDO;
use Throwable;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\services\TableRenderer\TableFacade;
use crm\src\_common\repositories\StatusRepository;
use crm\src\services\TableRenderer\TableDecorator;
use crm\src\_common\adapters\StatusValidatorAdapter;
use crm\src\components\StatusManagement\_entities\Status;
use crm\src\services\TableRenderer\TableRenderInput;
use crm\src\services\TableRenderer\TableTransformer;
use crm\src\services\TemplateRenderer\HeaderManager;
use crm\src\components\UserManagement\_entities\User;
use crm\src\services\TemplateRenderer\TemplateRenderer;
use crm\src\components\StatusManagement\StatusManagement;
use crm\src\services\TemplateRenderer\_common\TemplateBundle;

class StatusPage
{
    private StatusManagement $statusManagement;

    private TemplateRenderer $renderer;
    public function __construct(
        private string $projectPath,
        PDO $pdo,
        private LoggerInterface $logger = new NullLogger()
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
            $layout = (new TemplateBundle(
                templatePath: 'layout.tpl.php',
                variables: [
                    'body_js' => [
                        '/assets/js/app.js'
                    ]
                ],
                partialsContainer: 'content'
            ))
            ->addPartial((new TemplateBundle(
                templatePath: 'partials/head.tpl.php',
                variables: [
                    'title' => 'Тестовая страница',
                    'css' => [
                        '/assets/css/reset.css',
                        '/assets/css/fonts.css',
                        '/assets/css/styles.css',
                        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css'
                    ],
                    'js' => [
                        // '/assets/js/JsonRpcResponseHandler.js',
                        // '/assets/js/JsonRpcClient.js'
                    ]
                ],
                partialsContainer: 'head'
            )))
            ->addPartial(
                (new TemplateBundle(
                    templatePath: 'containers/page-container.tpl.php',
                    partialsContainer: 'main_container'
                ))->addPartial((new TemplateBundle(
                    templatePath: 'partials/main-menu.tpl.php',
                    partialsContainer: 'main_menu'
                )))
                ->addPartial((new TemplateBundle(
                    templatePath: 'partials/content.tpl.php',
                    variables: $components,
                    partialsContainer: 'content_container'
                )))
            );

            // Успешный ответ
            $headers->setResponseCode(200);
            echo $this->renderer->renderBundle($layout);
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
