<?php

namespace crm\src\components\BalanceManagement;

use crm\src\_common\interfaces\IValidation;
use crm\src\components\BalanceManagement\GetBalance;
use crm\src\components\BalanceManagement\CreateBalance;
use crm\src\components\BalanceManagement\DeleteBalance;
use crm\src\components\BalanceManagement\UpdateBalance;
use crm\src\components\LeadManagement\_common\interfaces\ILeadRepository;
use crm\src\components\BalanceManagement\_common\interfaces\IBalanceRepository;

class BalanceManagement
{
    private ?CreateBalance $create = null;
    private ?GetBalance $get = null;
    private ?UpdateBalance $update = null;
    private ?DeleteBalance $delete = null;

    public function __construct(
        private IBalanceRepository $repository,
        private IValidation $validator,
        private ILeadRepository $leadRepository
    ) {
    }

    public function create(): CreateBalance
    {
        return $this->create ??= new CreateBalance($this->repository, $this->validator);
    }

    public function get(): GetBalance
    {
        return $this->get ??= new GetBalance($this->repository, $this->leadRepository);
    }

    public function update(): UpdateBalance
    {
        return $this->update ??= new UpdateBalance($this->repository, $this->validator);
    }

    public function delete(): DeleteBalance
    {
        return $this->delete ??= new DeleteBalance($this->repository, $this->validator);
    }
}
