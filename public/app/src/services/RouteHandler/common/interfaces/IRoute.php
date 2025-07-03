<?php

declare(strict_types=1);

namespace crm\src\services\RouteHandler\common\interfaces;

interface IRoute
{
    public function setPattern(string $pattern): void;

    public function getPattern(): string;

    public function setClassName(string $className): void;

    public function getClassName(): string;

    public function setMethodName(?string $methodName): void;

    public function getMethodName(): ?string;

    /**
     * Установить дополнительные данные для всего маршрута (класса).
     *
     * @param array<string|int,mixed> $data
     */
    public function setExtraData(array $data): void;

    /**
     * Получить дополнительные данные для всего маршрута (класса).
     *
     * @return array<string|int,mixed>
     */
    public function getExtraData(): array;

    /**
     * Добавить одно дополнительное поле для всего маршрута (класса).
     *
     * @param string $key
     * @param mixed $value
     */
    public function addExtraData(string $key, mixed $value): void;

    /**
     * Установить дополнительные данные для метода.
     *
     * @param array<string|int,mixed> $data
     */
    public function setMethodExtraData(array $data): void;

    /**
     * Получить все дополнительные данные для метода.
     *
     * @return array<string|int,mixed>
     */
    public function getMethodExtraData(): array;

    /**
     * Добавить одно дополнительное поле для метода.
     *
     * @param string $key
     * @param mixed $value
     */
    public function addMethodExtraData(string $key, mixed $value): void;
}
