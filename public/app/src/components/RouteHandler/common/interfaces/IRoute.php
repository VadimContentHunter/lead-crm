<?php
declare(strict_types=1);

namespace crm\src\components\RouteHandler\common\interfaces;

interface IRoute
{
    public function setUrl(string $url): void;

    public function getUrl(): string;

    public function setClassName(string $className): void;

    public function getClassName(): string;

    public function setMethodName(?string $methodName): void;

    public function getMethodName(): ?string;

    /**
     * @param array<string,mixed>
     */
    public function setExtraData(array $data): void;

    /**
     * @return array<string,mixed>
     */
    public function getExtraData(): array;

    public function addExtraData(string $key, mixed $value): void;
}
