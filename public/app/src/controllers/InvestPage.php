<?php

namespace crm\src\controllers;

use Throwable;
use crm\src\services\AppContext\IAppContext;
use crm\src\services\TemplateRenderer\HeaderManager;
use crm\src\services\TemplateRenderer\TemplateRenderer;
use crm\src\services\TemplateRenderer\_common\TemplateBundle;

class InvestPage
{
    private TemplateRenderer $renderer;

    public function __construct(
        private IAppContext $appContext
    ) {
        $this->renderer = $this->appContext->getTemplateRenderer();

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
                            // 'component' => $this->getRenderTable($leads),
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
                                templatePath: 'partials/controlPanelP2p.tpl.php',
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
            '/assets/js/sidebarTriggers.js',
            '/assets/js/leadHandlers.js',
            '/assets/js/sourceHandlers.js',
            '/assets/js/statusHandlers.js',
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
        return [];
    }
}
