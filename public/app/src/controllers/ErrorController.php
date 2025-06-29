<?php
namespace crm\src\controllers;

use Throwable;

class ErrorController
{
    public function __construct(Throwable $exception)
    {
        // Заголовок ошибки
        http_response_code(500);

        $message = htmlspecialchars($exception->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $file = htmlspecialchars($exception->getFile(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $line = $exception->getLine();
        $trace = htmlspecialchars($exception->getTraceAsString(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        echo <<<HTML
            <!DOCTYPE html>
            <html lang="ru">
            <head>
            <meta charset="UTF-8">
            <title>Ошибка сервера</title>
            <style>
                body {
                    background: #f0f0f0;
                    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                    padding: 20px;
                    color: #333;
                }
                h1 {
                    color: #d32f2f;
                }
                .error-box {
                    background: white;
                    border: 1px solid #d32f2f;
                    padding: 20px;
                    border-radius: 6px;
                    max-width: 800px;
                    margin: 0 auto;
                    box-shadow: 0 0 10px rgba(211,47,47,0.3);
                }
                pre {
                    background: #f5f5f5;
                    border: 1px solid #ddd;
                    padding: 10px;
                    overflow-x: auto;
                    border-radius: 4px;
                    font-size: 14px;
                    line-height: 1.4;
                }
                small {
                    color: #666;
                }
            </style>
            </head>
            <body>
                <div class="error-box">
                    <h1>Произошла ошибка</h1>
                    <p><strong>Сообщение:</strong> {$message}</p>
                    <p><strong>Файл:</strong> {$file}</p>
                    <p><strong>Строка:</strong> {$line}</p>
                    <h2>Трассировка стека:</h2>
                    <pre>{$trace}</pre>
                </div>
            </body>
            </html>
        HTML;
    }
}
