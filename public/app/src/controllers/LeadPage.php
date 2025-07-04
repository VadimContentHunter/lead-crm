<?php

namespace crm\src\controllers;

use PDO;
use Throwable;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\controllers\NotFoundController;
use crm\src\controllers\API\DepositController;
use crm\src\services\TableRenderer\TableFacade;
use crm\src\_common\repositories\UserRepository;
use crm\src\_common\adapters\LeadValidatorAdapter;
use crm\src\_common\adapters\UserValidatorAdapter;
use crm\src\_common\repositories\SourceRepository;
use crm\src\_common\repositories\StatusRepository;
use crm\src\services\TableRenderer\TableDecorator;
use crm\src\_common\repositories\BalanceRepository;
use crm\src\_common\repositories\CommentRepository;
use crm\src\_common\repositories\DepositRepository;
use crm\src\_common\adapters\SourceValidatorAdapter;
use crm\src\_common\adapters\StatusValidatorAdapter;
use crm\src\services\TableRenderer\TableRenderInput;
use crm\src\services\TableRenderer\TableTransformer;
use crm\src\services\TemplateRenderer\HeaderManager;
use crm\src\_common\adapters\BalanceValidatorAdapter;
use crm\src\_common\adapters\CommentValidatorAdapter;
use crm\src\_common\adapters\DepositValidatorAdapter;
use crm\src\components\LeadManagement\_entities\Lead;
use crm\src\components\LeadManagement\LeadManagement;
use crm\src\components\UserManagement\_entities\User;
use crm\src\components\UserManagement\UserManagement;
use crm\src\services\TemplateRenderer\TemplateRenderer;
use crm\src\components\SourceManagement\_entities\Source;
use crm\src\components\SourceManagement\SourceManagement;
use crm\src\components\StatusManagement\_entities\Status;
use crm\src\components\StatusManagement\StatusManagement;
use crm\src\components\BalanceManagement\BalanceManagement;
use crm\src\components\CommentManagement\_entities\Comment;
use crm\src\components\CommentManagement\CommentManagement;
use crm\src\components\DepositManagement\DepositManagement;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;
use crm\src\services\TemplateRenderer\_common\TemplateBundle;
use crm\src\_common\repositories\LeadRepository\LeadRepository;
use crm\src\components\LeadManagement\_common\mappers\LeadMapper;
use crm\src\components\UserManagement\_common\mappers\UserMapper;
use crm\src\_common\repositories\LeadRepository\LeadSourceRepository;
use crm\src\_common\repositories\LeadRepository\LeadStatusRepository;
use crm\src\components\SourceManagement\_common\mappers\SourceMapper;
use crm\src\components\StatusManagement\_common\mappers\StatusMapper;
use crm\src\components\BalanceManagement\_common\mappers\BalanceMapper;
use crm\src\_common\repositories\LeadRepository\LeadAccountManagerRepository;

class LeadPage
{
    private LeadManagement $leadManagement;

    private SourceManagement $sourceManagement;

    private StatusManagement $statusManagement;

    private UserManagement $userManagement;

    private BalanceManagement $balanceManagement;

    private TemplateRenderer $renderer;

    private DepositManagement $depositManagement;

    private CommentManagement $commentManagement;

    public function __construct(
        private string $projectPath,
        PDO $pdo,
        private LoggerInterface $logger = new NullLogger()
    ) {
        $this->logger->info('LeadPage initialized');
        $this->renderer = new TemplateRenderer(baseTemplateDir: $this->projectPath . '/src/templates/');

        $this->commentManagement = new CommentManagement(
            new CommentRepository($pdo, $logger),
            new CommentValidatorAdapter()
        );

        $leadRepository = new LeadRepository($pdo, $logger);
        $this->leadManagement = new LeadManagement(
            leadRepository: $leadRepository,
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

        $this->userManagement = new UserManagement(
            new UserRepository($pdo, $logger),
            new UserValidatorAdapter()
        );

        $this->balanceManagement = new BalanceManagement(
            new BalanceRepository($pdo, $logger),
            new BalanceValidatorAdapter(),
            $leadRepository
        );

        $this->depositManagement = new DepositManagement(
            new DepositRepository($pdo, $logger),
            new DepositValidatorAdapter()
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

    public function showAddLeadPage(): void
    {
        $sourcesTitle = $this->sourceManagement->get()->executeAllMapped(function (Source $source) {
            return SourceMapper::toArray($source);
        })->getArray();

        $statusesTitle = $this->statusManagement->get()->executeAllMapped(function (Status $status) {
            return StatusMapper::toArray($status);
        })->getArray();

        $managersLogin = $this->userManagement->get()->executeAllMapped(function (User $user) {
            return UserMapper::toArray($user);
        })->getArray();

        $this->showPage([
            'components' => [(new TemplateBundle(
                templatePath: 'components/addLead.tpl.php',
                variables: [
                    'sourcesTitle' => $sourcesTitle,
                    'statusesTitle' => $statusesTitle,
                    'managersLogin' => $managersLogin
                ]
            ))]
        ]);
    }

    public function showAllLeadPage(): void
    {
        $sourcesList = $this->sourceManagement->get()->executeAllMapped(function (Source $source) {
            return SourceMapper::toArray($source);
        })->getArray();

        $statusesList = $this->statusManagement->get()->executeAllMapped(function (Status $status) {
            return StatusMapper::toArray($status);
        })->getArray();

        $managersList = $this->userManagement->get()->executeAllMapped(function (User $user) {
            return UserMapper::toArray($user);
        })->getArray();

        // $selectedData = [
        //   'sourceId' => $sourcesList->getSourceId(),
        //   'statusId' => $leadResult->getStatusId(),
        //   'accountManagerId' => $leadResult->getAccountManagerId()
        // ];

        // $headers = $this->leadManagement->get()->executeColumnNames()->getArray();
        // $all = $this->leadManagement->get()->all()->getArray();
        // $allB = $this->leadManagement->get()->all()->getArray();
        // $rows = $this->leadManagement->get()->executeAllMapped(function (Lead $lead) {
        //     return LeadMapper::toArray($lead);
        // })->getArray();

        $leadBalanceItems = $this->leadManagement->get()->all()->mapEach(function (Lead|array $lead) {
            $lead = is_array($lead) ? LeadMapper::fromArray($lead) : $lead;
            $lead = LeadMapper::toFlatViewArray($lead);
            $balance = $this->balanceManagement
                ->get()
                ->getByLeadId($lead['id'] ?? 0)
                ->first()
                ->mapData([BalanceMapper::class, 'toArray']);
            return array_merge(LeadMapper::toFlatViewArray($lead), $balance ?? []);
        });

        // Берём ключи ассоциативного массива + элементы индексного массива
        $headers = array_merge(
            array_keys(LeadMapper::toFlatViewArray(
                $this->leadManagement->get()->executeColumnNames()->getArray()
            )),
            $this->balanceManagement->get()->executeColumnNames()->getArray()
        );

        // Убираем возможные дубликаты
        // $headers = array_values(array_unique(array_merge(
        //     LeadMapper::toFlatViewArray($this->leadManagement->get()->executeColumnNames()->getArray()),
        //     // $this->leadManagement->get()->executeColumnNames()->getArray(),
        //     $this->balanceManagement->get()->executeColumnNames()->getArray()
        // )));

        $input = new TableRenderInput(
            header: $headers,
            rows: $leadBalanceItems->getArray(),
            attributes: ['id' => 'lead-table-1', 'data-module' => 'leads'],
            classes: ['base-table'],
            allowedColumns: [
                'id',
                'contact',
                'full_name',
                'account_manager',
                'address',
                'source',
                'status',
                'current',
                'drain',
                'potential',
            ],
            renameMap: [
                'full_name' => 'Полное имя',
                'account_manager' => 'Менеджер',
                'contact' => 'Контакт',
                'address' => 'Адрес',
                'source' => 'Источник',
                'status' => 'Статус',
                'current' => 'Текущие',
                'drain' => 'Потери',
                'potential' => 'Потенциал',
            ]
        );

        $tableFacade = new TableFacade(new TableTransformer(),  new TableDecorator());

        $this->showPage([
            'components' => [
                (new TemplateBundle(
                    templatePath: 'containers/average-in-line-component.tpl.php',
                    variables: [
                        'component' => $tableFacade->renderFilteredTable($input)->asHtml(),
                        'filterPanel' => (new TemplateBundle(
                            templatePath: 'partials/filtersLead.tpl.php',
                            variables: [
                                'sortColumns' => $headers,
                                'sourcesList' => $sourcesList,
                                'statusesList' => $statusesList,
                                'managersList' => $managersList,
                                'selectedData' => [],
                            ]
                        ))
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

        $leadResult = $this->leadManagement->get()->byId((int)$leadId);
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

        $managersLogin = $this->userManagement->get()->executeAllMapped(function (User $user) {
            return UserMapper::toArray($user);
        })->getArray();

        $commentsResult = $this->commentManagement->get()->executeAllMapped(function (Comment $comment) {
            return $comment->comment;
        })->getArray();

        $selectedData = [
          'sourceId' => $leadResult->getSourceId(),
          'statusId' => $leadResult->getStatusId(),
          'accountManagerId' => $leadResult->getAccountManagerId()
        ];

        $balanceResult = $this->balanceManagement->get()->getByLeadId((int)$leadId);
        $depositResult = $this->depositManagement->get()->getByLeadId((int)$leadId);
        // $commentsResult = $this->commentManagement->get()->getByLeadId($leadId);

        $this->showPage([
            'components' => [
                (new TemplateBundle(
                    templatePath: 'components/editLeadComentsForm.tpl.php',
                    variables: [
                        'comments' => $commentsResult,
                        'leadId' => $leadId,
                    ]
                )),
                (new TemplateBundle(
                    templatePath: 'components/editLeadMainForm.tpl.php',
                    variables: [
                        'leadId' => $leadId,
                        'sourcesTitle' => $sourcesTitle,
                        'statusesTitle' => $statusesTitle,
                        'managersLogin' => $managersLogin,
                        'selectedData' => $selectedData,
                        'fullName' => $leadResult->getFullName(),
                        'contact' => $leadResult->getContact(),
                        'address' => $leadResult->getAddress(),
                    ]
                )),
                (new TemplateBundle(
                    templatePath: 'components/editLeadBalanceForm.tpl.php',
                    variables: [
                        'current' => $balanceResult->getCurrent() ?? 0,
                        'drain' => $balanceResult->getDrain() ?? 0,
                        'potential' => $balanceResult->getPotential() ?? 0,
                        'leadId' => $balanceResult->getLeadId() ?? $leadId,
                        'id' => $balanceResult->getId() ?? 0
                    ]
                )),
                (new TemplateBundle(
                    templatePath: 'components/editLeadDepositForm.tpl.php',
                    variables: [
                        'sum' =>  $depositResult->getSum() ?? 0,
                        'txid' =>  $depositResult->getTxId() ?? '',
                        'leadId' => $depositResult->getLeadId() ?? $leadId,
                    ]
                )),
            ]
        ]);
    }
}
