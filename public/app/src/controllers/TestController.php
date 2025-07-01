<?php

namespace crm\src\controllers;

use Throwable;
use crm\src\services\TemplateRenderer\HeaderManager;
use crm\src\services\TemplateRenderer\TemplateRenderer;
use crm\src\services\TemplateRenderer\_common\TemplateBundle;

class TestController
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
                    'components' => [
                        (new TemplateBundle(templatePath: 'components/addUser.tpl.php')),
                        (new TemplateBundle(templatePath: 'components/baseForm.tpl.php')),
                        (new TemplateBundle(
                            templatePath: 'components/baseTable.tpl.php',
                            variables: [
                                'columns' => ['Название', 'Тип', 'Значение', 'Опции'],
                                'rows' => [
                                    ['Имя', 'text', ['type' => 'text', 'name' => 'username', 'value' => ''], 'Опция 1'],
                                    ['Возраст', 'number', ['type' => 'number', 'name' => 'age', 'value' => 25], 'Опция 2'],
                                    ['Пароль', 'password', ['type' => 'password', 'name' => 'pass'], ''],
                                    ['Статус', 'select', [
                                        'type' => 'select',
                                        'name' => 'status',
                                        'value' => 'active',
                                        'options' => ['active' => 'Активен', 'blocked' => 'Заблокирован']
                                    ], ''],
                                ]
                            ]
                        )),
                    ]
                    ],
                    partialsContainer: 'content_container'
                )))
            );

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
