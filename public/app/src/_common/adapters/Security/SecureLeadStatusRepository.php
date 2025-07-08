<?php

namespace crm\src\_common\adapters\Security;

use crm\src\components\Security\SecureWrapper;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_common\interfaces\IAccessGranter;
use crm\src\components\LeadManagement\_common\DTOs\StatusDto;
use crm\src\components\LeadManagement\_common\interfaces\ILeadStatusRepository;
use crm\src\components\StatusManagement\_entities\Status;
use crm\src\components\StatusManagement\_common\interfaces\IStatusRepository;

class SecureLeadStatusRepository extends SecureLeadRepository implements ILeadStatusRepository
{
    private SecureWrapper $secure;

    public function __construct(
        IStatusRepository $statusRepository,
        IAccessGranter $accessGranter,
        ?AccessContext $accessContext
    ) {
        $this->secure = new SecureWrapper($statusRepository, $accessGranter, $accessContext);
    }

    public function getByTitle(string $title): ?StatusDto
    {
        /**
 * @var Status|null $status
*/
        $status = $this->secure->__call('getByTitle', [$title]);
        return $status ? new StatusDto($status->id, $status->title) : null;
    }

    public function deleteByTitle(string $title): ?int
    {
        return $this->secure->__call('deleteByTitle', [$title]);
    }
}
