<?php

namespace crm\src\controllers;

use Throwable;
use crm\src\services\TemplateRenderer\HeaderManager;
use crm\src\services\TemplateRenderer\TemplateRenderer;
use crm\src\services\JsonRpcLowComponent\JsonRpcServerFacade;
use crm\src\services\TemplateRenderer\_common\TemplateBundle;

class UserPage
{
    private TemplateRenderer $renderer;
    public function __construct(
        private string $projectPath,
    ) {
        $this->renderer = new TemplateRenderer(baseTemplateDir: $this->projectPath . '/src/templates/');
    }

    public function showAddUserPage(): void
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
                    variables: [
                    'components' => [(new TemplateBundle(templatePath: 'components/addUser.tpl.php'))]
                    ],
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
}
