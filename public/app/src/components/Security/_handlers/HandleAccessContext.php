<?php

namespace crm\src\components\Security\_handlers;

use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_common\interfaces\IAccessContextRepository;
use RuntimeException;

class HandleAccessContext
{
    public static string $secretKey = 'secretKey';

    public function __construct(
        private IAccessContextRepository $repository
    ) {
    }

    public function generateSessionHash(string $login, string $passwordHash): string
    {
        $data = $login . ':' . $passwordHash;
        return hash_hmac('sha256', $data, self::$secretKey);
    }

    /**
     * Создание нового AccessContext с обработкой ошибки сохранения.
     */
    public function createAccess(
        int $userId,
        ?string $sessionAccessHash = null,
        ?int $roleId = null,
        ?int $spaceId = null,
    ): AccessContext {
        $context = new AccessContext(
            userId: $userId,
            sessionAccessHash: $sessionAccessHash,
            roleId: $roleId,
            spaceId: $spaceId,
        );

        $savedId = $this->repository->save($context);

        if ($savedId <= 0) {
            throw new RuntimeException('Failed to save AccessContext');
        }

        $context->id = $savedId;

        return $context;
    }

    public function checkAccessBySessionHash(string $sessionAccessHash): ?AccessContext
    {
        return $this->repository->getBySessionHash($sessionAccessHash);
    }

    public function delAccessBySessionHash(string $sessionAccessHash): bool
    {
        return $this->repository->deleteBySessionHash($sessionAccessHash);
    }
}
