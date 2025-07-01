<?php

namespace crm\src\services\JsonRpcLowComponent;

use RuntimeException;
use InvalidArgumentException;

class JsonRpcRequestDecoder
{
    /**
     * @var mixed[]
     */
    private array $data;
    private ?string $raw = null;

    /**
     * @param string|mixed[]|null $source
     */
    public function __construct(string|array|null $source = null)
    {
        if ($source === null) {
            $this->raw = file_get_contents('php://input') ?: '';
            $this->data = json_decode($this->raw, true) ?? [];
        } elseif (is_string($source)) {
            $this->raw = $source;
            $this->data = json_decode($source, true) ?? [];
        } elseif (is_array($source)) {
            $this->data = $source;
        } else {
            throw new InvalidArgumentException('Unsupported input type');
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('JSON parse error: ' . json_last_error_msg(), -32700);
        }

        if (!isset($this->data['jsonrpc'], $this->data['method'], $this->data['id'])) {
            throw new RuntimeException('Invalid JSON-RPC 2.0 request', -32600);
        }
    }

    public function getJson(): ?string
    {
        return $this->raw;
    }

    /**
     * @return mixed[]
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function getMethod(): string
    {
        return $this->data['method'];
    }

    /**
     * @return mixed[]
     */
    public function getParams(): array
    {
        return is_array($this->data['params'] ?? null) ? $this->data['params'] : [];
    }

    public function getId(): mixed
    {
        return $this->data['id'];
    }

    public function getVersion(): string
    {
        return $this->data['jsonrpc'] ?? '2.0';
    }

    public function isValid(): bool
    {
        return isset($this->data['jsonrpc'], $this->data['method'], $this->data['id']);
    }
}
