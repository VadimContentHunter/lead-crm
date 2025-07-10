<?php

namespace crm\src\controllers;

use Throwable;
use crm\src\services\AppContext\IAppContext;
use crm\src\services\TemplateRenderer\HeaderManager;
use crm\src\services\TemplateRenderer\TemplateRenderer;
use crm\src\services\TemplateRenderer\_common\TemplateBundle;

class TestController
{
    private TemplateRenderer $renderer;
    private HeaderManager $headers;

    public function __construct(
        private IAppContext $appContext
    ) {
        $this->headers = new HeaderManager();
        $this->headers->set('Content-Type', 'text/html; charset=utf-8');

        $this->renderer = $this->appContext->getTemplateRenderer();
        $this->renderer->setHeaders($this->headers);

        $this->showPage([
            'components' => [(new TemplateBundle(
                templatePath: 'test.tpl.php',
            ))]
        ]);
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
}
