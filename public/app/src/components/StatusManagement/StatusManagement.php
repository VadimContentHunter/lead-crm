<?php

namespace crm\src\components\StatusManagement;

use crm\src\_common\interfaces\IValidation;
use crm\src\components\StatusManagement\GetStatus;
use crm\src\components\StatusManagement\_common\interfaces\IStatusRepository;

class StatusManagement
{
    private ?CreateStatus $create = null;
    private ?GetStatus $get = null;
    private ?UpdateStatus $update = null;
    private ?DeleteStatus $delete = null;

    public function __construct(
        private IStatusRepository $repository,
        private IValidation $validator,
    ) {
    }

    public function create(): CreateStatus
    {
        return $this->create ??= new CreateStatus($this->repository, $this->validator);
    }

    public function get(): GetStatus
    {
        return $this->get ??= new GetStatus($this->repository, $this->validator);
    }

    public function update(): UpdateStatus
    {
        return $this->update ??= new UpdateStatus($this->repository, $this->validator);
    }

    public function delete(): DeleteStatus
    {
        return $this->delete ??= new DeleteStatus($this->repository, $this->validator);
    }
}
