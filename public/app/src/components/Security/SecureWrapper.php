<?php

namespace crm\src\components\Security;

use Exception;
use Throwable;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_exceptions\SecurityException;
use crm\src\components\Security\_common\interfaces\IAccessGranter;
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
      * @param array<int, mixed> $constructorArgs
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

        // if ($accessContext === null) {
        //     header('Location: /login');
        //     exit;
        //     // throw new SecurityException("Пользователь не авторизован.");
        // }

        try {
            if (!$accessGranter->canCreate($className, $accessContext)) {
                $userId = $accessContext->userId ?? 0;
                throw new SecurityException("Доступ к классу $className запрещен для пользователя {$userId}");
            }
        } catch (AuthenticationRequiredException  $e) {
            // Перенаправляем на страницу логина
            header('Location: /login');
            exit;
        }

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
            // throw new SecurityException("Пользователь не авторизован.");
        }

        if (!method_exists($this->target, $method)) {
            throw new SecurityException("Метод $method не существует в целевом объекте.");
        }

        if (!$this->accessGranter->canCall($this->target, $method, $args, $this->accessContext)) {
            header('Location: /access-denied');
            exit;
            // throw new SecurityException("Доступ к методу $method запрещен для пользователя {$this->accessContext->userId}");
        }

        return $this->target->$method(...$args);
    }
}
