<?php

namespace crm\src\components\Security;

use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_common\interfaces\IAccessContextRepository;

class SessionAuthManager
{
    public static string $sessionAccessHashKey = 'session_access_hash';

    public function __construct(
        private IAccessContextRepository $repository
    ) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login(string $sessionAccessHash): void
    {
        $_SESSION[self::$sessionAccessHashKey] = $sessionAccessHash;
    }

    public function checkAccess(): bool
    {
        $hash = $_SESSION[self::$sessionAccessHashKey] ?? '';
        return $this->repository->getBySessionHash($hash) !== null;
    }

    public function logout(): void
    {
        $hash = $_SESSION[self::$sessionAccessHashKey] ?? '';
        // $this->repository->deleteBySessionHash($hash);
        unset($_SESSION[self::$sessionAccessHashKey]);
        session_destroy();
    }

    public function getCurrentAccessContext(): ?AccessContext
    {
        $hash = $_SESSION[self::$sessionAccessHashKey] ?? '';
        return $this->repository->getBySessionHash($hash);
    }
}
