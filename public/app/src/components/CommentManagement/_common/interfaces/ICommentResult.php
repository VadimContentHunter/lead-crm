<?php

namespace crm\src\components\CommentManagement\_common\interfaces;

use DateTime;
use crm\src\_common\interfaces\IResult;
use crm\src\components\CommentManagement\_entities\Comment;

interface ICommentResult extends IResult
{
    public function getComment(): ?Comment;

    public function getId(): ?int;

    public function getCommentText(): ?string;

    public function getUserId(): ?int;

    public function getLeadId(): ?int;

    public function getDepositId(): ?int;

    public function getCreatedAt(): ?DateTime;
}
