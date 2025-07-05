<?php

namespace crm\src\components\Security;

use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_common\DTOs\SessionInputDto;
use crm\src\components\Security\_common\interfaces\IAccessContextRepository;

class HandleAccessContext
{
    public static string $secretKey = 'secretKey';

    public function __construct(
        private IAccessContextRepository $repository
    ) {
    }

    /**
     * Генерация session hash для текущего AccessContext.
     */
    public function generateSessionHash(string $login, string $passwordHash): string
    {
        // $passwordHash = password_hash('securePassword', PASSWORD_DEFAULT);
        $data = $login . ':' . $passwordHash;
        return hash_hmac('sha256', $data, self::$secretKey);
    }

    public function createAccess(AccessContext $accContextDbDto): bool
    {
        if ($accContextDbDto->userId > 0 && $this->repository->save($accContextDbDto) > 0) {
            return true;
        }

        return false;
    }

    public function checkAccessBySessionHash(AccessContext|string $sessionInputDto): ?AccessContext
    {
        $sessionAccessHash = is_string($sessionInputDto) ? $sessionInputDto : $sessionInputDto->sessionAccessHash;
        return $this->repository->getBySessionHash($sessionAccessHash);
    }

    public function delAccessBySessionHash(AccessContext $accContextDbDto): bool
    {
        return $this->repository->deleteBySessionHash($accContextDbDto->sessionAccessHash);
    }
}
