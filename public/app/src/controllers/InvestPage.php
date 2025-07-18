<?php

namespace crm\src\controllers;

use Throwable;
use crm\src\services\AppContext\IAppContext;
use crm\src\services\TemplateRenderer\HeaderManager;
use crm\src\components\UserManagement\_entities\User;
use crm\src\Investments\_application\InvestmentService;
use crm\src\services\TemplateRenderer\TemplateRenderer;
use crm\src\services\TemplateRenderer\_common\TemplateBundle;
use crm\src\components\UserManagement\_common\mappers\UserMapper;

class InvestPage
{
    private TemplateRenderer $renderer;

    private InvestmentService $service;

    public function __construct(
        private IAppContext $appContext
    ) {
        $this->renderer = $this->appContext->getTemplateRenderer();
        $this->service = $this->appContext->getInvestmentService();

        $this->renderPage();
    }

    public function renderPage(): void
    {
        $this->show(
            components: [
                'components' => [
                    (new TemplateBundle(
                        templatePath: 'containers/average-in-line-component.tpl.php',
                        variables: [
                            'component' => $this->service->getInvLeadTable()->getString() ?? '',
                            // 'filterPanel' => (new TemplateBundle(
                            //     templatePath: 'partials/filtersLead.tpl.php',
                            //     variables: [
                            //     'sortColumns' => $headers,
                            //     'sourcesList' => $sourcesList,
                            //     'statusesList' => $statusesList,
                            //     'managersList' => $managersList,
                            //     'selectedData' => [],
                            //     ]
                            // )),
                            'controlPanel' => (new TemplateBundle(
                                templatePath: 'partials/controlPanelInvest.tpl.php',
                            )),
                        ]
                    ))
                ]
            ],
            overlay_items: $this->getSidebar()
        );
    }

    /**
     * @param array<string, mixed> $components
     * @param array<string, mixed> $overlay_items
     */
    public function show(array $components = [], array $overlay_items = [], string|TemplateBundle $rightSidebar = ''): void
    {
        // $renderer = new TemplateRenderer(baseTemplateDir: $this->projectPath . '/src/templates/');
        // $layout = (new TemplateBundle(templatePath: 'components/addUser.tpl.php'));
        $headers = new HeaderManager();
        $headers->set('Content-Type', 'text/html; charset=utf-8');
        $this->renderer->setHeaders($headers);

        $scripts = [
            '/assets/js/invest/invSidebarTriggers.js',
            '/assets/js/invest/invLeadHandlers.js',
            '/assets/js/invest/invSourceHandlers.js',
            '/assets/js/invest/invStatusHandlers.js',
        ];

        try {
            // Успешный ответ
            $headers->setResponseCode(200);
            echo $this->renderer->renderBundle(
                $this->appContext->getLayout($components, $overlay_items, $rightSidebar, $scripts)
            );
        } catch (Throwable $e) {
            // Внутренняя ошибка — HTTP 500
            $headers->setResponseCode(500);
            // header('Content-Type: text/plain; charset=utf-8');
            // echo "Произошла ошибка: " . $e->getMessage();
            throw $e;
        }
    }

    /**
     * @return TemplateBundle[]
     */
    public function getSidebar(): array
    {
        $invStatusSidebar = (new TemplateBundle(
            templatePath: 'containers/wrapperSideBar.tpl.php',
            variables: [
                'classId' => 'inv-status-menu-id',
                'addPanel' => (new TemplateBundle(templatePath: 'components/invest/addInvStatus.tpl.php')),
                'table' => (new TemplateBundle(
                    templatePath: 'containers/average-in-line-component.tpl.php',
                    variables: [
                        'component' => $this->service->getStatusTable()->getString() ?? '',
                    ]
                ))
            ]
        ));

        $invSourceSideBar = (new TemplateBundle(
            templatePath: 'containers/wrapperSideBar.tpl.php',
            variables: [
                'classId' => 'inv-source-menu-id',
                'addPanel' => (new TemplateBundle(templatePath: 'components/invest/addInvSource.tpl.php')),
                'table' => (new TemplateBundle(
                    templatePath: 'containers/average-in-line-component.tpl.php',
                    variables: [
                        'component' => $this->service->getSourceTable()->getString() ?? '',
                    ]
                ))
            ]
        ));

        $invLeadSideBar = (new TemplateBundle(
            templatePath: 'containers/wrapperSideBar.tpl.php',
            variables: [
                'classId' => 'inv-lead-menu-id',
                'addPanel' => (new TemplateBundle(
                    templatePath: 'components/invest/addInvLead.tpl.php',
                    variables: [
                        'sourcesTitle' => [],
                        'statusesTitle' => [],
                        'managersLogin' => []
                    ]
                )),
            ]
        ));

        return [$invLeadSideBar, $invStatusSidebar, $invSourceSideBar];
    }
}
