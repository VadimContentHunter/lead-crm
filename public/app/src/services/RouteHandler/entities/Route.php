<?php

declare(strict_types=1);

namespace crm\src\services\RouteHandler\entities;

use crm\src\services\RouteHandler\common\interfaces\IRoute;

class Route implements IRoute
{
    /**
     * @param string $pattern         Паттерн URL в виде регулярного
     *                                выражения (без // и флагов)
     * @param string $className       Имя класса
     *                                контроллера
     * @param string|null $methodName      Имя метода
     *                                     (опционально)
     * @param array<string|int,mixed> $extraData       Данные
     *                                                 для
     *                                                 класса
     * @param array<string|int,mixed> $methodExtraData Данные для метода
     */
    public function __construct(
        private string $pattern,
        private string $className,
        private ?string $methodName = null,
        private array $extraData = [],
        private array $methodExtraData = []
    ) {
    }

    public function setPattern(string $pattern): void
    {
        $this->pattern = $pattern;
    }

    public function getPattern(): string
    {
        return $this->pattern;
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

    public function setExtraData(array $data): void
    {
        $this->extraData = $data;
    }

    public function getExtraData(): array
    {
        return $this->extraData;
    }

    public function addExtraData(string $key, mixed $value): void
    {
        $this->extraData[$key] = $value;
    }

    public function setMethodExtraData(array $data): void
    {
        $this->methodExtraData = $data;
    }

    public function getMethodExtraData(): array
    {
        return $this->methodExtraData;
    }

    public function addMethodExtraData(string $key, mixed $value): void
    {
        $this->methodExtraData[$key] = $value;
    }
}
