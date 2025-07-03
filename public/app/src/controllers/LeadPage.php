<?php

namespace crm\src\controllers;

use PDO;
use Throwable;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\controllers\NotFoundController;
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
use crm\src\components\LeadManagement\_entities\Lead;
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
use crm\src\components\LeadManagement\_common\mappers\LeadMapper;
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

    public function showAllLeadPage(): void
    {
        $headers = $this->leadManagement->get()->executeColumnNames()->getArray();
        // $all = $this->leadManagement->get()->all()->getArray();
        $rows = $this->leadManagement->get()->executeAllMapped(function (Lead $lead) {
            return LeadMapper::toArray($lead);
        })->getArray();

        $input = new TableRenderInput(
            header: $headers,
            rows: $rows,
            attributes: ['id' => 'lead-table-1', 'data-module' => 'leads'],
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

    public function showEditLeadPage(string|int $leadId): void
    {
        if (!filter_var($leadId, FILTER_VALIDATE_INT)) {
            (new NotFoundController())->show404();
            exit();
        }

        $leadResult = $this->leadManagement->get()->byId($leadId);
        if (!$leadResult->isSuccess()) {
            (new NotFoundController())->show404();
            exit();
        }



        $sourcesTitle = $this->sourceManagement->get()->executeAllMapped(function (Source $source) {
            return SourceMapper::toArray($source);
        })->getArray();

        $statusesTitle = $this->statusManagement->get()->executeAllMapped(function (Status $status) {
            return StatusMapper::toArray($status);
        })->getArray();

        $selectedData = [
          'sourceId' => $leadResult->getSourceId(),
          'statusId' => $leadResult->getStatusId(),
          'accountManagerId' => $leadResult->getAccountManagerId()
        ];

        $this->showPage([
            'components' => [
                (new TemplateBundle(
                    templatePath: 'components/editLeadComentsForm.tpl.php',
                    variables: [
                        'comments' => [
                            '[2025-07-02 12:00:00] [INFO] Lead created',
                            '[2025-07-02 12:05:00] [COMMENT] Связался с клиентом',
                            '[2025-07-02 12:10:00] [STATUS] Status changed to In Progress',
                            '[2025-07-02 12:15:00] [COMMENT] Добавлены детали сделки',
                            '[2025-07-02 12:20:00] [INFO] Lead updated',
                            '[2025-07-02 12:25:00] [COMMENT] Назначена встреча',
                            '[2025-07-02 12:30:00] [STATUS] Status changed to Completed',
                            '[2025-07-02 12:35:00] [COMMENT] Отправлено коммерческое предложение',
                            '[2025-07-02 12:40:00] [INFO] Lead closed',
                            '[2025-07-02 12:45:00] [COMMENT] Клиент подтвердил сделку',
                            '[2025-07-02 12:00:00] [INFO] Lead created',
                            '[2025-07-02 12:05:00] [COMMENT] Связался с клиентом',
                            '[2025-07-02 12:10:00] [STATUS] Status changed to In Progress',
                        ],

                    ]
                )),
                (new TemplateBundle(
                    templatePath: 'components/editLeadMainForm.tpl.php',
                    variables: [
                        'sourcesTitle' => $sourcesTitle,
                        'statusesTitle' => $statusesTitle,
                        'selectedData' => $selectedData,
                        'fullName' => $leadResult->getFullName(),
                        'contact' => $leadResult->getContact(),
                        'address' => $leadResult->getAddress(),
                    ]
                )),
                (new TemplateBundle(
                    templatePath: 'components/editLeadBalanceForm.tpl.php',
                    variables: [
                        'current' => 0,
                        'drain' => 0,
                        'potential' => 0
                    ]
                )),
                (new TemplateBundle(
                    templatePath: 'components/editLeadDepositForm.tpl.php',
                    variables: [
                        'drain' => 0,
                        'txid' => '',
                    ]
                )),
            ]
        ]);
    }
}
