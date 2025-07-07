<?php

namespace crm\src\components\Security\_exceptions;

use RuntimeException;
use crm\src\components\Security\_exceptions\SecurityException;

/**
 * Исключение, которое сигнализирует о необходимости аутентификации пользователя.
 * Может использоваться для перенаправления на страницу логина.
 */
class AuthenticationRequiredException extends SecurityException
{
    public function __construct(
        string $message = 'Authentication is required.',
        int $code = 401,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
