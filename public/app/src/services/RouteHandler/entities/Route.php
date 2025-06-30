<?php

declare(strict_types=1);

namespace  crm\src\services\RouteHandler\entities;

use  crm\src\services\RouteHandler\common\interfaces\IRoute;

class Route implements IRoute
{
    /**
     * @param string $pattern    Паттерн URL в виде регулярного
     *                           выражения (без разделителей // и флагов)
     *                           Пример: ^user/(?P<id>\d+)$ — паттерн для URL с
     *                           параметром "id"
     * @param string $className  Имя класса
     *                           контроллера
     * @param string|null $methodName Метод контроллера (опционально)
     * @param array<string,mixed> $extraData  Дополнительные
     *                                        данные маршрута
     */
    public function __construct(
        private string $pattern,
        private string $className,
        private ?string $methodName = null,
        private array $extraData = []
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

    /**
     * @param array<string,mixed> $data
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
