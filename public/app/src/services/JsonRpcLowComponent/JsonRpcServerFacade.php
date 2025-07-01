<?php

namespace crm\src\services\JsonRpcLowComponent;

use Throwable;
use crm\src\services\JsonRpcLowComponent\JsonRpcRequestDecoder;
use crm\src\services\JsonRpcLowComponent\JsonRpcResponseBuilder;

class JsonRpcServerFacade
{
    private JsonRpcRequestDecoder $decoder;

    public function __construct()
    {
        try {
            $this->decoder = new JsonRpcRequestDecoder();
        } catch (Throwable $e) {
            $this->send(JsonRpcResponseBuilder::error(
                $e->getCode() ?: -32700,
                $e->getMessage(),
                null
            ));
            exit;
        }
    }

    public function getMethod(): string
    {
        return $this->decoder->getMethod();
    }

    public function getParams(): array
    {
        return $this->decoder->getParams();
    }

    public function getId(): mixed
    {
        return $this->decoder->getId();
    }

    public function send(array $response): never
    {
        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public function replyData(array $messages): never
    {
        $this->send(JsonRpcResponseBuilder::data($messages, $this->getId()));
    }

    public function replyContentUpdate(string $selector, string $content): never
    {
        $this->send(JsonRpcResponseBuilder::contentUpdate($selector, $content, $this->getId()));
    }

    public function replyError(int $code, string $message): never
    {
        $this->send(JsonRpcResponseBuilder::error($code, $message, $this->getId()));
    }
}
