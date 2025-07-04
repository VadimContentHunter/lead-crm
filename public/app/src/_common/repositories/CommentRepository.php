<?php

namespace crm\src\_common\repositories;

use crm\src\_common\interfaces\ARepository;
use crm\src\components\CommentManagement\_entities\Comment;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;
use crm\src\components\CommentManagement\_common\mappers\CommentMapper;
use crm\src\components\CommentManagement\_common\interfaces\ICommentRepository;

/**
 * @extends ARepository<Comment>
 */
class CommentRepository extends ARepository implements ICommentRepository
{
    protected function getTableName(): string
    {
        return 'comments';
    }

    /**
     * @return class-string<Comment>
     */
    protected function getEntityClass(): string
    {
        return Comment::class;
    }

    protected function fromArray(): callable
    {
        return [CommentMapper::class, 'fromArray'];
    }

    protected function toArray(object $entity): array
    {
        /**
         * @var Comment $entity
        */
        return CommentMapper::toArray($entity);
    }

    public function getByLeadId(int $leadId): array
    {
        return $this->repository->executeQuery(
            (new QueryBuilder())
                ->table($this->getTableName())
                ->where('lead_id = :lead_id')
                ->select(['lead_id' => $leadId])
        )->getValidMappedList([CommentMapper::class, 'fromArray']);
    }

    public function getByUserId(?int $userId): array
    {
        return $this->repository->executeQuery(
            (new QueryBuilder())
                ->table($this->getTableName())
                ->where($userId !== null ? 'user_id = :user_id' : 'user_id IS NULL')
                ->select($userId !== null ? ['user_id' => $userId] : [])
        )->getValidMappedList([CommentMapper::class, 'fromArray']);
    }

    public function getByDepositId(?int $depositId): array
    {
        return $this->repository->executeQuery(
            (new QueryBuilder())
                ->table($this->getTableName())
                ->where($depositId !== null ? 'deposit_id = :deposit_id' : 'deposit_id IS NULL')
                ->select($depositId !== null ? ['deposit_id' => $depositId] : [])
        )->getValidMappedList([CommentMapper::class, 'fromArray']);
    }

    public function deleteByLeadId(int $leadId): ?int
    {
        return $this->repository->executeQuery(
            (new QueryBuilder())
                ->table($this->getTableName())
                ->where('lead_id = :lead_id')
                ->delete(['lead_id' => $leadId])
        )->getInt();
    }

    public function deleteByUserId(?int $userId): ?int
    {
        return $this->repository->executeQuery(
            (new QueryBuilder())
                ->table($this->getTableName())
                ->where($userId !== null ? 'user_id = :user_id' : 'user_id IS NULL')
                ->delete($userId !== null ? ['user_id' => $userId] : [])
        )->getInt();
    }

    public function deleteByDepositId(?int $depositId): ?int
    {
        return $this->repository->executeQuery(
            (new QueryBuilder())
                ->table($this->getTableName())
                ->where($depositId !== null ? 'deposit_id = :deposit_id' : 'deposit_id IS NULL')
                ->delete($depositId !== null ? ['deposit_id' => $depositId] : [])
        )->getInt();
    }
}
