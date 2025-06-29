<?php
declare(strict_types=1);

namespace crm\src\components\RouteHandler\common\interfaces;

interface IRoute
{
    public function setPattern(string $pattern): void;

    public function getPattern(): string;

    public function setClassName(string $className): void;

    public function getClassName(): string;

    public function setMethodName(?string $methodName): void;

    public function getMethodName(): ?string;

    /**
     * @param array<string,mixed> $data
     */
    public function setExtraData(array $data): void;

    /**
     * @return array<string,mixed>
     */
    public function getExtraData(): array;

    public function addExtraData(string $key, mixed $value): void;
}
