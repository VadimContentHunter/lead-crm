<?php

namespace crm\src\controllers;

use Throwable;
use crm\src\controllers\LeadPage;
use crm\src\controllers\SourcePage;
use crm\src\controllers\StatusPage;
use crm\src\services\AppContext\IAppContext;
use crm\src\services\TemplateRenderer\HeaderManager;
use crm\src\services\TemplateRenderer\TemplateRenderer;
use crm\src\components\SourceManagement\_entities\Source;
use crm\src\services\TemplateRenderer\_common\TemplateBundle;

class P2pPage
{
    private TemplateRenderer $renderer;

    private StatusPage $statusPage;

    private SourcePage $sourcePage;

    private LeadPage $leadPage;

    public function __construct(
        private IAppContext $appContext
    ) {
        $this->renderer = $this->appContext->getTemplateRenderer();

        $this->statusPage = new StatusPage($this->appContext);
        $this->sourcePage = new SourcePage($this->appContext);
        $this->leadPage = new LeadPage($this->appContext);

        $this->renderPage();
    }

    public function renderPage(): void
    {
        $this->show(
            components: [
                'components' => [
                    $this->leadPage->getTableStatusComponent()
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

        try {
            // Успешный ответ
            $headers->setResponseCode(200);
            echo $this->renderer->renderBundle($this->appContext->getLayout($components, $overlay_items, $rightSidebar));
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
        $statusSidebar = (new TemplateBundle(
            templatePath: 'components/statusSideBar.tpl.php',
            variables: [
                'addPanel' => (new TemplateBundle(templatePath: 'components/addStatus.tpl.php')),
                'table' => $this->statusPage->getTableStatusComponent()
            ]
        ));

        $sourceSideBar = (new TemplateBundle(
            templatePath: 'components/sourceSideBar.tpl.php',
            variables: [
                'addPanel' => (new TemplateBundle(templatePath: 'components/addSource.tpl.php')),
                'table' => $this->sourcePage->getTableSourceComponent()
            ]
        ));

        return [$statusSidebar, $sourceSideBar];
    }
}
