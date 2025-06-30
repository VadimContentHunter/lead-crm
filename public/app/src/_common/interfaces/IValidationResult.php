<?php

namespace crm\src\_common\interfaces;

interface IValidationResult
{
    /**
     * Возвращает true, если валидация прошла успешно.
     */
    public function isValid(): bool;

    /**
     * Возвращает список ошибок валидации (если есть).
     *
     * @return string[] Список сообщений об ошибках.
     */
    public function getErrors(): array;
}
