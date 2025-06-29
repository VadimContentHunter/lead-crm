<?php
namespace crm\src\controllers;

class NotFoundController
{
    public function show404(): void
    {
        // Отправляем HTTP заголовок с кодом 404
        http_response_code(404);

        // Выводим сообщение
        echo "Ошибка 404: Страница не найдена.";
    }
}
