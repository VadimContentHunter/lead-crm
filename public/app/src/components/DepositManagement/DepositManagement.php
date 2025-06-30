<?php

namespace crm\src\components\DepositManagement;

use crm\src\_common\interfaces\IValidation;
use crm\src\components\DepositManagement\GetDeposit;
use crm\src\components\DepositManagement\CreateDeposit;
use crm\src\components\DepositManagement\DeleteDeposit;
use crm\src\components\DepositManagement\UpdateDeposit;
use crm\src\components\DepositManagement\_common\interfaces\IDepositRepository;

class DepositManagement
{
    private ?CreateDeposit $create = null;
    private ?GetDeposit $get = null;
    private ?UpdateDeposit $update = null;
    private ?DeleteDeposit $delete = null;

    public function __construct(
        private IDepositRepository $repository,
        private IValidation $validator,
    ) {
    }

    public function create(): CreateDeposit
    {
        return $this->create ??= new CreateDeposit($this->repository, $this->validator);
    }

    public function get(): GetDeposit
    {
        return $this->get ??= new GetDeposit($this->repository, $this->validator);
    }

    public function update(): UpdateDeposit
    {
        return $this->update ??= new UpdateDeposit($this->repository, $this->validator);
    }

    public function delete(): DeleteDeposit
    {
        return $this->delete ??= new DeleteDeposit($this->repository, $this->validator);
    }
}
