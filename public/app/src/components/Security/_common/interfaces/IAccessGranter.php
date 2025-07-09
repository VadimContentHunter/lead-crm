<?php

namespace crm\src\components\Security\_common\interfaces;

use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_exceptions\SecurityException;

interface IAccessGranter
{
    public function canCreate(string $className, ?AccessContext $accessContext): bool;

    /**
     * Выполняет вызов метода с проверкой доступа.
     *
     * @param  object $target        Целевой объект.
     * @param  string $methodName    Имя вызываемого метода.
     * @param  mixed[] $args          Аргументы метода.
     * @param  AccessContext $accessContext Контекст доступа.
     * @return mixed Результат вызова метода.
     *
     * @throws SecurityException Если доступ запрещён.
     */
    public function callWithAccessCheck(object $target, string $methodName, array $args, ?AccessContext $accessContext): mixed;
}
