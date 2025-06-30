<?php

namespace crm\src\components\BalanceManagement;

use crm\src\_common\interfaces\IValidation;
use crm\src\components\BalanceManagement\_usecases\GetBalance;
use crm\src\components\BalanceManagement\_usecases\CreateBalance;
use crm\src\components\BalanceManagement\_usecases\DeleteBalance;
use crm\src\components\BalanceManagement\_usecases\UpdateBalance;
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
    ) {
    }

    public function create(): CreateBalance
    {
        return $this->create ??= new CreateBalance($this->repository, $this->validator);
    }

    public function get(): GetBalance
    {
        return $this->get ??= new GetBalance($this->repository);
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
