<?php

namespace crm\src\components\Security\_exceptions;

use Throwable;
use crm\src\services\JsonRpcLowComponent\JsonRpcResponseBuilder;

/**
 * Базовое исключение для всех ошибок безопасности, возвращаемых в формате JSON-RPC.
 */
class JsonRpcSecurityException extends SecurityException
{
    public function __construct(
        string $message = 'Security error',
        int $code = -32000,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Формирует JSON-RPC ошибку.
     *
     * @param  mixed $id
     * @return array<string, mixed>
     */
    public function toJsonRpcError(mixed $id): array
    {
        return JsonRpcResponseBuilder::error(
            $this->getCode() ?: -32000,
            $this->getMessage(),
            $id
        );
    }
}
