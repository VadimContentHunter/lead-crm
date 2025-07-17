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

class InvestEditPage
{
    private TemplateRenderer $renderer;

    // private InvestmentService $service;

    /**
     * @param IAppContext $appContext
     */
    public function __construct(
        private IAppContext $appContext
    ) {
        $this->renderer = $this->appContext->getTemplateRenderer();
        // $this->service = $this->appContext->getInvestmentService();

        // $this->renderPage();
    }

    public function renderPage(string|int $leadId): void
    {
        // $params['lead_id'] = $leadId;
        // $accountManagerLogin = $this->appContext->getUserManagement()->get()->executeById($userId)->getLogin() ?? '—';
        // $managersLogin = $this->appContext->getUserManagement()->get()->executeAll()
        //     ->mapEach(function (User $user) {
        //         $userArray = UserMapper::toArray($user);
        //         if ($userArray['login'] === null) {
        //             return null;
        //         }

        //         return [
        //             'value' => $userArray['id'],
        //             'text' => $userArray['login'],
        //         ];
        //     })
        //     ->getArray();
        // array_unshift($managersLogin, ['value' => '', 'text' => '— Выберите менеджера —', 'selected' => true]);

        // $result = $this->service->getFormCreateData(
        //     $params,
        //     [
        //         'account_manager_id' => $managersLogin
        //     ]
        // );

        // if ($result->isSuccess()) {
        // } else {
        //     $errorMessage = $result->getError()?->getMessage() ?? 'Произошла ошибка';
        // }

        $this->show(
            components: [
                'components' => [
                    (new TemplateBundle(
                        templatePath: 'containers/edit-lead-wrapper.tpl.php',
                        partialsContainer: 'deposit',
                        variables: [
                            'controlPanel' => (new TemplateBundle(
                                templatePath: 'partials/controlPanelEditInvestLead.tpl.php',
                            )),
                            'components' => [
                                (new TemplateBundle(
                                    templatePath: 'components/invest/editInvLeadMainForm.tpl.php',
                                    variables: [
                                        // 'leadId' => $leadId,
                                        // 'sourcesTitle' => $sourcesTitle,
                                        // 'statusesTitle' => $statusesTitle,
                                        // 'managersLogin' => $managersLogin,
                                        // 'selectedData' => $selectedData,
                                        // 'fullName' => $leadResult->getFullName(),
                                        // 'contact' => $leadResult->getContact(),
                                        // 'address' => $leadResult->getAddress(),
                                    ]
                                )),
                                // (new TemplateBundle(
                                //     templatePath: 'components/editLeadBalanceForm.tpl.php',
                                //     variables: [
                                //         'current' => $balanceResult->getCurrent() ?? 0,
                                //         'drain' => $balanceResult->getDrain() ?? 0,
                                //         'potential' => $balanceResult->getPotential() ?? 0,
                                //         'leadId' => $balanceResult->getLeadId() ?? $leadId,
                                //         'id' => $balanceResult->getId() ?? 0
                                //     ]
                                // )),
                                // (new TemplateBundle(
                                //     templatePath: 'components/editLeadDepositForm.tpl.php',
                                //     variables: [
                                //         'sum' =>  $depositResult->getSum() ?? 0,
                                //         'txid' =>  $depositResult->getTxId() ?? '',
                                //         'leadId' => $depositResult->getLeadId() ?? $leadId,
                                //     ]
                                // )),
                            ]
                        ],
                    )),
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
            '/assets/js/invest/invEditPageSidebarTriggers.js',
            '/assets/js/invest/invEditPageHandlers.js',
            // '/assets/js/invest/invSourceHandlers.js',
            // '/assets/js/invest/invStatusHandlers.js',
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
        // $commentsResult = $this->commentManagement->get()->executeAllMapped(function (Comment $comment) {
        //     return $comment->comment;
        // })->getArray();

        // $commentsSideBar = (new TemplateBundle(
        //     templatePath: 'containers/wrapperSideBar.tpl.php',
        //     variables: [
        //         'classId' => 'history-menu-id',
        //         'addPanel' => (new TemplateBundle(
        //             templatePath: 'components/editLeadComentsForm.tpl.php',
        //             variables: [
        //                 'comments' => $commentsResult,
        //                 'leadId' => $leadId,
        //             ]
        //         )),
        //     ]
        // ));

        // return [$commentsSideBar];
        return [];
    }
}
