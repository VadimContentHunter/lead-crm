<?php

namespace crm\src\controllers;

use Throwable;

class ErrorController
{
    public function __construct(Throwable $exception, array $params)
    {
        $isWarning = $params['warning'] ?? false;

        if (!headers_sent()) {
            http_response_code($isWarning ? 200 : 500);
        }

        $message = htmlspecialchars($exception->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $file = htmlspecialchars($exception->getFile(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $line = $exception->getLine();
        $trace = htmlspecialchars($exception->getTraceAsString(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $header = $isWarning ? '⚠️ Предупреждение' : '❌ Ошибка';
        $boxId = $isWarning ? 'warning-block' : 'error-block';
        $highlightColor = $isWarning ? '#f9a825' : '#c62828';

        echo <<<HTML
        <style>
            #{$boxId} {
                background-color: #fff;
                border-left: 6px solid {$highlightColor};
                padding: 20px;
                margin: 20px auto;
                max-width: 800px;
                font-family: Consolas, monospace;
                font-size: 14px;
                color: #222;
                box-shadow: 0 0 10px rgba(0,0,0,0.05);
                border-radius: 4px;
            }
            #{$boxId} h1 {
                margin: 0 0 15px 0;
                color: {$highlightColor};
                font-size: 18px;
            }
            #{$boxId} p {
                margin: 6px 0;
            }
            #{$boxId} pre {
                background: #f5f5f5;
                padding: 12px;
                border: 1px solid #ccc;
                border-radius: 4px;
                overflow-x: auto;
            }
            #{$boxId} strong {
                display: inline-block;
                width: 100px;
                color: #444;
            }
        </style>
        <div id="{$boxId}">
            <h1>{$header}</h1>
            <p><strong>Сообщение:</strong> {$message}</p>
            <p><strong>Файл:</strong> {$file}</p>
            <p><strong>Строка:</strong> {$line}</p>
            <h2>Трассировка:</h2><pre>{$trace}</pre>
        </div>
        HTML;
    }
}
