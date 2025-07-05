<?php

namespace crm\src\components\Security;

use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_common\interfaces\IAccessGranter;

class SecureWrapperFactory
{
    private static ?IAccessGranter $accessGranter = null;

    /**
     * Инициализация фабрики. Нужно вызывать один раз при старте приложения.
     */
    public static function init(IAccessGranter $accessGranter): void
    {
        self::$accessGranter = $accessGranter;
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
     * Основной метод для создания SecureWrapper
     */
    public static function create(
        object $target,
        int $userId,
        ?string $sessionAccessHash = null,
        ?int $roleId = null,
        ?int $spaceId = null,
        ?int $id = null
    ): SecureWrapper {
        $accessContext = new AccessContext(
            userId: $userId,
            sessionAccessHash: $sessionAccessHash,
            roleId: $roleId,
            spaceId: $spaceId,
            id: $id
        );

        return new SecureWrapper($target, self::getAccessGranter(), $accessContext);
    }

    /**
     * Альтернативный метод с готовым AccessContext
     */
    public static function createWithContext(
        object $target,
        AccessContext $accessContext
    ): SecureWrapper {
        return new SecureWrapper($target, self::getAccessGranter(), $accessContext);
    }
}
