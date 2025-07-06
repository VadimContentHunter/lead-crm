<?php

namespace crm\src\components\Security\_handlers;

use RuntimeException;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_common\mappers\AccessContextMapper;
use crm\src\components\Security\_common\interfaces\IAccessContextRepository;

class HandleAccessContext
{
    public static string $secretKey = 'secretKey';

    public function __construct(
        private IAccessContextRepository $repository
    ) {
    }

    public function generateSessionHash(string $login, string $passwordHash): ?string
    {
        if (
            empty($login) || empty($passwordHash) ||
            mb_strlen($login) < 3 || mb_strlen($passwordHash) < 3
        ) {
            return null;
        }

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
    ): ?AccessContext {
        $context = new AccessContext(
            userId: $userId,
            sessionAccessHash: $sessionAccessHash,
            roleId: $roleId,
            spaceId: $spaceId,
        );

        $savedId = $this->repository->save(AccessContextMapper::toNonEmptyArray($context));

        if ($savedId <= 0) {
            return null;
        }

        $context->id = $savedId;

        return $context;
    }

    public function updateAccess(AccessContext $accessContext): bool
    {
        return $this->repository->update(AccessContextMapper::toNonEmptyArray($accessContext)) !== null ? true : false;
    }

    public function updateSessionHash(int $userId, string $login, string $passwordHash): bool
    {
        $accessContext = $this->repository->getByUserId($userId);
        if ($accessContext === null) {
            return false;
        }

        $accessContext->sessionAccessHash = $this->generateSessionHash($login, $passwordHash);

        if ($accessContext->sessionAccessHash === null) {
            return false;
        }
        return $this->updateAccess($accessContext);
    }

    public function verifySessionHash(int $userId, string $login, string $passwordHash): bool
    {
        $accessContext = $this->repository->getByUserId($userId);
        if ($accessContext === null) {
            return false;
        }

        return $accessContext->sessionAccessHash === $this->generateSessionHash($login, $passwordHash);
    }

    public function verifySessionHashBySession(int $userId, string $sessionAccessHash): bool
    {
        $accessContext = $this->repository->getByUserId($userId);
        if ($accessContext === null) {
            return false;
        }

        return $accessContext->sessionAccessHash === $sessionAccessHash;
    }

    public function generateSessionHashByUserId(int $userId): ?string
    {
        $accessContext = $this->repository->getByUserId($userId);
        if ($accessContext === null) {
            return null;
        }

        return $accessContext->sessionAccessHash;
    }

    public function checkAccessBySessionHash(string $sessionAccessHash): bool
    {
        return $this->repository->getBySessionHash($sessionAccessHash) !== null ? true : false;
    }

    public function delAccessBySessionHash(string $sessionAccessHash): bool
    {
        return $this->repository->deleteBySessionHash($sessionAccessHash);
    }

    public function delAccessById(int $id): bool
    {
        return $this->repository->deleteById($id) !== null ? true : false;
    }
}
