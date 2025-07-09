<?php

namespace crm\src\_common\adapters\Security\BasedAccessGranter;

use crm\src\components\Security\_common\DTOs\AccessFullContextDTO;

/**
 * Интерфейс для обработчиков доступа на уровне ролей.
 */
interface IRoleAccessHandler
{
    /**
     * Определяет, может ли этот обработчик обработать вызов target + method.
     *
     * @param  object $target
     * @param  string $methodName
     * @return bool
     */
    public function supports(object $target, string $methodName): bool;

    /**
     * Выполняет обработку вызова target::methodName с учётом AccessFullContext.
     *
     * @param  AccessFullContextDTO $context
     * @param  object               $target
     * @param  string               $methodName
     * @param  mixed[]              $args
     * @return mixed
     *
     * @throws \Throwable В случае запрета на вызов
     */
    public function handle(
        AccessFullContextDTO $context,
        object $target,
        string $methodName,
        array $args
    ): mixed;
}
