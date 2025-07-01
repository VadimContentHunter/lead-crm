<?php

namespace crm\src\services\JsonRpcLowComponent;

class JsonRpcResponseBuilder
{
    /**
     * @return array<string,mixed>
     */
    public static function result(mixed $result, mixed $id): array
    {
        return [
            'jsonrpc' => '2.0',
            'result' => $result,
            'id' => $id,
        ];
    }

    /**
     * @return array<string,mixed>
     */
    public static function error(int $code, string $message, mixed $id = null): array
    {
        return [
            'jsonrpc' => '2.0',
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
            'id' => $id,
        ];
    }

    /**
     * @param mixed[] $messages
     *
     * @return array<string,mixed>
     */
    public static function data(array $messages, mixed $id): array
    {
        return self::result(['type' => 'data', 'payload' => $messages], $id);
    }

    /**
     * @return array<string,mixed>
     */
    public static function contentUpdate(string $selector, string $content, mixed $id): array
    {
        return self::result(['type' => 'content_update', 'selector' => $selector, 'content' => $content], $id);
    }
}
