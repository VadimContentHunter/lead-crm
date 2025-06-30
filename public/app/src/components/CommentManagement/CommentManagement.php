<?php

namespace crm\src\components\CommentManagement;

use crm\src\_common\interfaces\IValidation;
use crm\src\components\CommentManagement\GetComment;
use crm\src\components\CommentManagement\CreateComment;
use crm\src\components\CommentManagement\DeleteComment;
use crm\src\components\CommentManagement\UpdateComment;
use crm\src\components\CommentManagement\_common\interfaces\ICommentRepository;

class CommentManagement
{
    private ?CreateComment $create = null;
    private ?GetComment $get = null;
    private ?UpdateComment $update = null;
    private ?DeleteComment $delete = null;

    public function __construct(
        private ICommentRepository $repository,
        private IValidation $validator,
    ) {
    }

    public function create(): CreateComment
    {
        return $this->create ??= new CreateComment($this->repository, $this->validator);
    }

    public function get(): GetComment
    {
        return $this->get ??= new GetComment($this->repository);
    }

    public function update(): UpdateComment
    {
        return $this->update ??= new UpdateComment($this->repository, $this->validator);
    }

    public function delete(): DeleteComment
    {
        return $this->delete ??= new DeleteComment($this->repository, $this->validator);
    }
}
