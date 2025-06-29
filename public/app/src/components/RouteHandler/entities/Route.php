<?php
declare(strict_types=1);

namespace crm\src\components\RouteHandler\entities;

use crm\src\components\RouteHandler\common\interfaces\IRoute;

class Route implements IRoute
{
    public function __construct(
        private string $url,
        private string $className,
        private ?string $methodName = null,
        private array $extraData = []
    ) {}

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setClassName(string $className): void
    {
        $this->className = $className;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function setMethodName(?string $methodName): void
    {
        $this->methodName = $methodName;
    }

    public function getMethodName(): ?string
    {
        return $this->methodName;
    }

    /**
     * @param array<string,mixed>
     */
    public function setExtraData(array $data): void
    {
        $this->extraData = $data;
    }

    /**
     * @return array<string,mixed>
     */
    public function getExtraData(): array
    {
        return $this->extraData;
    }

    public function addExtraData(string $key, mixed $value): void
    {
        $this->extraData[$key] = $value;
    }
}
