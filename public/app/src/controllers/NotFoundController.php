<?php

namespace crm\src\controllers;

class NotFoundController
{
    public function show404(): void
    {
        // Отправляем HTTP заголовок с кодом 404
        http_response_code(404);

        // Выводим стильную страницу 404
        echo <<<HTML
            <!DOCTYPE html>
            <html lang="ru">
            <head>
                <meta charset="UTF-8" />
                <title>Ошибка 404 - Страница не найдена</title>
                <style>
                    body {
                        background: #f8f9fa;
                        color: #343a40;
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                        display: flex;
                        flex-direction: column;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        margin: 0;
                        text-align: center;
                    }
                    h1 {
                        font-size: 8rem;
                        margin: 0;
                        color: #dc3545;
                    }
                    p {
                        font-size: 1.5rem;
                        margin: 20px 0 30px;
                    }
                    a {
                        color: #007bff;
                        text-decoration: none;
                        font-weight: 600;
                        border: 2px solid #007bff;
                        padding: 10px 25px;
                        border-radius: 5px;
                        transition: background-color 0.3s ease, color 0.3s ease;
                    }
                    a:hover {
                        background-color: #007bff;
                        color: #fff;
                    }
                </style>
            </head>
            <body>
                <h1>404</h1>
                <p>Упс! Страница, которую вы ищете, не найдена.</p>
                <a href="/">Вернуться на главную</a>
            </body>
            </html>
            HTML;
    }
}
