<?php

namespace crm\src\_common\adapters\Security;

use crm\src\components\Security\SecureWrapper;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_common\interfaces\IAccessGranter;
use crm\src\components\LeadManagement\_common\DTOs\SourceDto;
use crm\src\components\LeadManagement\_common\interfaces\ILeadSourceRepository;
use crm\src\components\SourceManagement\_entities\Source;
use crm\src\components\SourceManagement\_common\interfaces\ISourceRepository;

/**
 * Secure обёртка для SourceRepository с преобразованием в SourceDto.
 */
class SecureLeadSourceRepository extends SecureLeadRepository implements ILeadSourceRepository
{
    private SecureWrapper $secure;

    public function __construct(
        ISourceRepository $sourceRepository,
        IAccessGranter $accessGranter,
        ?AccessContext $accessContext
    ) {
        $this->secure = new SecureWrapper($sourceRepository, $accessGranter, $accessContext);
    }

    public function getByTitle(string $title): ?SourceDto
    {
        /**
         * @var Source|null $source
         */
        $source = $this->secure->__call('getByTitle', [$title]);
        return $source ? new SourceDto($source->id, $source->title) : null;
    }

    public function deleteByTitle(string $title): ?int
    {
        return $this->secure->__call('deleteByTitle', [$title]);
    }
}
