<?php

namespace crm\src\services\TemplateRenderer;

class HeaderManager
{
    /**
     * @var mixed[]
     */
    private array $headers = [];
    private ?int $code = null;

    public function set(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    public function add(string $name, string $value): void
    {
        if (!isset($this->headers[$name])) {
            $this->headers[$name] = $value;
        } else {
            $this->headers[$name] .= ', ' . $value;
        }
    }

    /**
     * @return mixed[]
     */
    public function getAll(): array
    {
        return $this->headers;
    }

    public function setResponseCode(int $code): void
    {
        $this->code = $code;
    }

    public function getResponseCode(): ?int
    {
        return $this->code;
    }

    public static function json(int $code = 200): self
    {
        $self = new self();
        $self->set('Content-Type', 'application/json');
        $self->setResponseCode($code);
        return $self;
    }

    public static function html(int $code = 200): self
    {
        $self = new self();
        $self->set('Content-Type', 'text/html; charset=utf-8');
        $self->setResponseCode($code);
        return $self;
    }

    public static function fileDownload(string $filename, string $contentType = 'application/octet-stream'): self
    {
        $self = new self();
        $self->set('Content-Type', $contentType);
        $self->set('Content-Disposition', "attachment; filename=\"$filename\"");
        return $self;
    }
}
