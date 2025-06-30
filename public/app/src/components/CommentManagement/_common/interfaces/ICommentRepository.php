<?php

namespace crm\src\components\CommentManagement\_common\interfaces;

use crm\src\_common\interfaces\IRepository;
use crm\src\components\CommentManagement\_entities\Comment;

/**
 * @extends IRepository<Comment>
 */
interface ICommentRepository extends IRepository
{
    /**
     * Получить комментарии по ID лида
     *
     * @param  int $leadId
     * @return Comment[]
     */
    public function getByLeadId(int $leadId): array;

    /**
     * Получить комментарии по ID пользователя.
     * Если передать null, вернуть комментарии с user_id IS NULL.
     *
     * @param  int|null $userId
     * @return Comment[]
     */
    public function getByUserId(?int $userId): array;

    /**
     * Получить комментарии по ID депозита.
     * Если передать null, вернуть комментарии с deposit_id IS NULL.
     *
     * @param  int|null $depositId
     * @return Comment[]
     */
    public function getByDepositId(?int $depositId): array;
}
