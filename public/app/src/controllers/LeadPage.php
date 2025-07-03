<?php

namespace crm\src\controllers;

use PDO;
use Throwable;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\services\TableRenderer\TableFacade;
use crm\src\_common\repositories\UserRepository;
use crm\src\_common\adapters\LeadValidatorAdapter;
use crm\src\_common\adapters\UserValidatorAdapter;
use crm\src\_common\repositories\SourceRepository;
use crm\src\_common\repositories\StatusRepository;
use crm\src\services\TableRenderer\TableDecorator;
use crm\src\_common\adapters\SourceValidatorAdapter;
use crm\src\_common\adapters\StatusValidatorAdapter;
use crm\src\services\TableRenderer\TableRenderInput;
use crm\src\services\TableRenderer\TableTransformer;
use crm\src\services\TemplateRenderer\HeaderManager;
use crm\src\components\LeadManagement\_entities\User;
use crm\src\components\LeadManagement\LeadManagement;
use crm\src\services\TemplateRenderer\TemplateRenderer;
use crm\src\components\SourceManagement\_entities\Source;
use crm\src\components\SourceManagement\SourceManagement;
use crm\src\components\StatusManagement\_entities\Status;
use crm\src\components\StatusManagement\StatusManagement;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;
use crm\src\services\TemplateRenderer\_common\TemplateBundle;
use crm\src\_common\repositories\LeadRepository\LeadRepository;
use crm\src\_common\repositories\LeadRepository\LeadSourceRepository;
use crm\src\_common\repositories\LeadRepository\LeadStatusRepository;
use crm\src\components\SourceManagement\_common\mappers\SourceMapper;
use crm\src\components\StatusManagement\_common\mappers\StatusMapper;
use crm\src\_common\repositories\LeadRepository\LeadAccountManagerRepository;

class LeadPage
{
    private LeadManagement $leadManagement;

    private SourceManagement $sourceManagement;

    private StatusManagement $statusManagement;

    private TemplateRenderer $renderer;
    public function __construct(
        private string $projectPath,
        PDO $pdo,
        private LoggerInterface $logger = new NullLogger()
    ) {
        $this->renderer = new TemplateRenderer(baseTemplateDir: $this->projectPath . '/src/templates/');
        $this->leadManagement = new LeadManagement(
            leadRepository: new LeadRepository($pdo, $logger),
            sourceRepository: new LeadSourceRepository($pdo, $logger),
            statusRepository: new LeadStatusRepository($pdo, $logger),
            accountManagerRepository: new LeadAccountManagerRepository($pdo, $logger),
            validator: new LeadValidatorAdapter()
        );

        $this->sourceManagement = new SourceManagement(
            new SourceRepository($pdo, $logger),
            new SourceValidatorAdapter()
        );

        $this->statusManagement = new StatusManagement(
            new StatusRepository($pdo, $logger),
            new StatusValidatorAdapter()
        );
    }

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

    public function showAddLeadPage(): void
    {
        $sourcesTitle = $this->sourceManagement->get()->executeAllMapped(function (Source $source) {
            return SourceMapper::toArray($source);
        })->getArray();

        $statusesTitle = $this->statusManagement->get()->executeAllMapped(function (Status $status) {
            return StatusMapper::toArray($status);
        })->getArray();

        $this->showPage([
            'components' => [(new TemplateBundle(
                templatePath: 'components/addLead.tpl.php',
                variables: [
                    'sourcesTitle' => $sourcesTitle,
                    'statusesTitle' => $statusesTitle
                ]
            ))]
        ]);
    }

    public function showAllUserPage(): void
    {
        // $headers = $this->LeadManagement->get()->executeColumnNames()->getArray();
        // $rows = $this->LeadManagement->get()->executeAllMapped(function (User $user) {
        //     return [
        //         'id' => $user->id,
        //         'login' => $user->login,
        //         'password_hash' => '',
        //     ];
        // })->getArray();

        // $input = new TableRenderInput(
        //     header: $headers,
        //     rows: $rows,
        //     attributes: ['id' => 'user-table-1', 'data-module' => 'users'],
        //     classes: ['base-table']
        // );

        // $tableFacade = new TableFacade(new TableTransformer(),  new TableDecorator());

        // $this->showPage([
        //     'components' => [
        //         (new TemplateBundle(
        //             templatePath: 'containers/average-in-line-component.tpl.php',
        //             variables: [
        //             'component' => $tableFacade->renderTable($input)->asHtml()
        //             ]
        //         ))
        //     ]
        // ]);
    }
}
