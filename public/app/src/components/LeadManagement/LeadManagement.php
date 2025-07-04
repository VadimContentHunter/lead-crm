<?php

namespace crm\src\components\LeadManagement;

use crm\src\_common\interfaces\IValidation;
use crm\src\components\LeadManagement\GetLead;
use crm\src\components\LeadManagement\CreateLead;
use crm\src\components\LeadManagement\DeleteLead;
use crm\src\components\LeadManagement\UpdateLead;
use crm\src\components\LeadManagement\_common\interfaces\ILeadRepository;
use crm\src\components\LeadManagement\_common\interfaces\ILeadSourceRepository;
use crm\src\components\LeadManagement\_common\interfaces\ILeadStatusRepository;
use crm\src\components\LeadManagement\_common\interfaces\ILeadAccountManagerRepository;

class LeadManagement
{
    private ?CreateLead $create = null;
    private ?GetLead $get = null;
    private ?UpdateLead $update = null;
    private ?DeleteLead $delete = null;

    public function __construct(
        private ILeadRepository $leadRepository,
        private ILeadSourceRepository $sourceRepository,
        private ILeadStatusRepository $statusRepository,
        private ILeadAccountManagerRepository $accountManagerRepository,
        private IValidation $validator,
    ) {
    }

    public function create(): CreateLead
    {
        return $this->create ??= new CreateLead(
            $this->leadRepository,
            $this->sourceRepository,
            $this->statusRepository,
            $this->accountManagerRepository,
            $this->validator
        );
    }

    public function get(): GetLead
    {
        return $this->get ??= new GetLead(
            repository: $this->leadRepository,
            sourceRepository: $this->sourceRepository,
            statusRepository: $this->statusRepository,
            accManagerRepository: $this->accountManagerRepository
        );
    }

    public function update(): UpdateLead
    {
        return $this->update ??= new UpdateLead(
            $this->leadRepository,
            $this->sourceRepository,
            $this->statusRepository,
            $this->accountManagerRepository,
            $this->validator
        );
    }

    public function delete(): DeleteLead
    {
        return $this->delete ??= new DeleteLead(
            $this->leadRepository
        );
    }
}
