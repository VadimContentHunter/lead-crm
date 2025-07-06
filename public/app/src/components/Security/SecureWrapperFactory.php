<?php

namespace crm\src\components\Security;

use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_common\interfaces\IAccessGranter;

class SecureWrapperFactory
{
    private static ?IAccessGranter $accessGranter = null;

    private static ?AccessContext $accessContext = null;

    /**
     * Инициализация фабрики. Нужно вызывать один раз при старте приложения.
     */
    public static function init(IAccessGranter $accessGranter, AccessContext $accessContext): void
    {
        self::$accessGranter = $accessGranter;
        self::$accessContext = $accessContext;
    }

    /**
     * Проверка и получение AccessGranter
     */
    private static function getAccessGranter(): IAccessGranter
    {
        if (self::$accessGranter === null) {
            throw new \RuntimeException('SecureWrapperFactory is not initialized. Call SecureWrapperFactory::init() first.');
        }

        return self::$accessGranter;
    }

    /**
     * Проверка и получение AccessContext
     */
    private static function getAccessContext(): AccessContext
    {
        if (self::$accessContext === null) {
            throw new \RuntimeException('SecureWrapperFactory is not initialized. Call SecureWrapperFactory::init() first.');
        }

        return self::$accessContext;
    }

    /**
     * Альтернативный метод с готовым AccessContext
     */
    public static function createSecureWrapper(
        object $target
    ): SecureWrapper {
        return new SecureWrapper($target, self::getAccessGranter(), self::getAccessContext());
    }
}
