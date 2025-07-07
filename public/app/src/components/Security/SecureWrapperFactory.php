<?php

namespace crm\src\components\Security;

use crm\src\services\AppContext\AppContext;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_common\interfaces\IAccessGranter;

class SecureWrapperFactory
{
    private static ?IAccessGranter $accessGranter = null;

    public static ?AccessContext $accessContext = null;

    // public static ?AppContext $appContext = null;

    /**
     * Инициализация фабрики. Нужно вызывать один раз при старте приложения.
     */
    public static function init(IAccessGranter $accessGranter, ?AccessContext $accessContext): void
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
    private static function getAccessContext(): ?AccessContext
    {
        // if (self::$accessContext === null) {
        //     throw new \RuntimeException('SecureWrapperFactory is not initialized. Call SecureWrapperFactory::init() first.');
        // }

        return self::$accessContext;
    }

    /**
     * Оборачивает уже существующий объект в SecureWrapper с текущим контекстом безопасности.
     */
    public static function wrapExistingObject(
        object $target
    ): SecureWrapper {
        return new SecureWrapper($target, self::getAccessGranter(), self::getAccessContext());
    }

    /**
     * Создаёт новый объект указанного класса и оборачивает его в SecureWrapper с текущим контекстом безопасности.
     */
    public static function createAndWrapObject(
        string $className,
        array $constructorArgs = []
    ): SecureWrapper {
        return SecureWrapper::createWrapped(
            $className,
            $constructorArgs,
            self::getAccessGranter(),
            self::getAccessContext()
        );
    }
}
