<?php

namespace crm\src\components\SourceManagement;

use crm\src\_common\interfaces\IValidation;
use crm\src\components\SourceManagement\GetSource;
use crm\src\components\SourceManagement\_common\interfaces\ISourceRepository;

class SourceManagement
{
    private ?CreateSource $create = null;
    private ?GetSource $get = null;
    private ?UpdateSource $update = null;
    private ?DeleteSource $delete = null;

    public function __construct(
        private ISourceRepository $repository,
        private IValidation $validator,
    ) {
    }

    public function create(): CreateSource
    {
        return $this->create ??= new CreateSource($this->repository, $this->validator);
    }

    public function get(): GetSource
    {
        return $this->get ??= new GetSource($this->repository, $this->validator);
    }

    public function update(): UpdateSource
    {
        return $this->update ??= new UpdateSource($this->repository, $this->validator);
    }

    public function delete(): DeleteSource
    {
        return $this->delete ??= new DeleteSource($this->repository, $this->validator);
    }
}
