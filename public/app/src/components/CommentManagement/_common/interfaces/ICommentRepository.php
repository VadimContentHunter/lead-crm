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
     * Возвращает комментарии по leadId.
     *
     * @param  int $leadId
     * @return Comment[]
     */
    public function getByLeadId(int $leadId): array;

    /**
     * Возвращает комментарии по userId или null (для поиска с user_id IS NULL).
     *
     * @param  int|null $userId
     * @return Comment[]
     */
    public function getByUserId(?int $userId): array;

    /**
     * Возвращает комментарии по depositId или null (для поиска с deposit_id IS NULL).
     *
     * @param  int|null $depositId
     * @return Comment[]
     */
    public function getByDepositId(?int $depositId): array;

    /**
     * Удаляет комментарии по leadId.
     *
     * @param  int $leadId
     * @return int|null ID удалённых записей или null при ошибке
     */
    public function deleteByLeadId(int $leadId): ?int;

    /**
     * Удаляет комментарии по userId или null (удаляет комментарии с user_id IS NULL).
     *
     * @param  int|null $userId
     * @return int|null ID удалённых записей или null при ошибке
     */
    public function deleteByUserId(?int $userId): ?int;

    /**
     * Удаляет комментарии по depositId или null (удаляет комментарии с deposit_id IS NULL).
     *
     * @param  int|null $depositId
     * @return int|null ID удалённых записей или null при ошибке
     */
    public function deleteByDepositId(?int $depositId): ?int;
}
