<?php

namespace crm\src\components\Security;

use Exception;
use Throwable;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_exceptions\SecurityException;
use crm\src\components\Security\_common\interfaces\IAccessGranter;
use crm\src\components\Security\_exceptions\JsonRpcSecurityException;
use crm\src\components\Security\_exceptions\AuthenticationRequiredException;

class SecureWrapper
{
    public function __construct(
        private object $target,
        private IAccessGranter $accessGranter,
        private ?AccessContext $accessContext
    ) {
    }

     /**
      * Фабрика: создаёт целевой объект и оборачивает в SecureWrapper.
      *
      * @param class-string $className
      * @param array<int,mixed> $constructorArgs
      */
    public static function createWrapped(
        string $className,
        array $constructorArgs,
        IAccessGranter $accessGranter,
        ?AccessContext $accessContext
    ): self {
        if (!class_exists($className)) {
            throw new SecurityException("Класс $className не найден.");
        }

        try {
            if (!$accessGranter->canCreate($className, $accessContext)) {
                $userId = $accessContext->userId ?? 0;
                throw new SecurityException("Доступ к классу $className запрещен для пользователя {$userId}");
            }
        } catch (AuthenticationRequiredException $e) {
            header('Location: /login');
            exit;
        }

        // Создаём экземпляр динамического класса с передачей аргументов конструктора
        $target = new $className(...$constructorArgs);

        return new self($target, $accessGranter, $accessContext);
    }


    /**
     * @param mixed[] $args
     */
    public function __call(string $method, array $args): mixed
    {
        if ($this->accessContext === null) {
            header('Location: /login');
            exit;
        }

        try {
            return $this->accessGranter->callWithAccessCheck(
                $this->target,
                $method,
                $args,
                $this->accessContext
            );
        } catch (JsonRpcSecurityException $e) {
            // Отправляем JSON-RPC ошибку
            throw $e;
        } catch (SecurityException $se) {
            header('Location: /access-denied?message=' . urlencode($se->getMessage()));
            exit;
        }
    }
}
