<?php

namespace crm\src\controllers;

use Throwable;
use crm\src\services\TemplateRenderer\HeaderManager;
use crm\src\services\TemplateRenderer\TemplateRenderer;
use crm\src\services\TemplateRenderer\_common\TemplateBundle;

class LoginController
{
    private TemplateRenderer $renderer;
    private HeaderManager $headers;

    public function __construct(
        private string $projectPath
    ) {
        $this->headers = new HeaderManager();
        $this->headers->set('Content-Type', 'text/html; charset=utf-8');

        $this->renderer = new TemplateRenderer(baseTemplateDir: $this->projectPath . '/src/templates/');
        $this->renderer->setHeaders($this->headers);

        $this->show();
    }

    public function show(): void
    {
        try {
            $layout = (new TemplateBundle(
                templatePath: 'layout.tpl.php',
                variables: [],
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
                        '/assets/js/app.js',
                        '/assets/js/analytics.js'
                    ]
                ],
                partialsContainer: 'head'
            )))
            ->addPartial((new TemplateBundle(
                templatePath: 'containers/login-container.tpl.php',
                partialsContainer: 'main_container'
            )));

            // Успешный ответ
            $this->headers->setResponseCode(200);
            echo $this->renderer->renderBundle($layout);
        } catch (Throwable $e) {
            // Внутренняя ошибка — HTTP 500
            $this->headers->setResponseCode(500);
            // header('Content-Type: text/plain; charset=utf-8');
            // echo "Произошла ошибка: " . $e->getMessage();
            throw $e;
        }
    }
}
