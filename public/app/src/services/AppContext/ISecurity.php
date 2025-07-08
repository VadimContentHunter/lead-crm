<?php

namespace crm\src\services\AppContext;

use crm\src\components\Security\SecureWrapper;

interface ISecurity
{
    /**
     * Оборачивает переданный объект в SecureWrapper с текущим контекстом доступа.
     *
     * @template T of object
     * @param    T $target Объект, который нужно обернуть
     * @return   SecureWrapper&T Обёрнутый объект (типизированный SecureWrapper)
     */
    public function wrapWithSecurity(object $target): SecureWrapper;
}
