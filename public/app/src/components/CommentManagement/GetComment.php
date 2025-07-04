<?php

namespace crm\src\components\CommentManagement;

use Throwable;
use crm\src\components\CommentManagement\_common\interfaces\ICommentResult;
use crm\src\components\CommentManagement\_common\adapters\CommentResult;
use crm\src\components\CommentManagement\_common\interfaces\ICommentRepository;
use crm\src\components\CommentManagement\_exceptions\CommentManagementException;
use crm\src\_common\interfaces\IResult;

class GetComment
{
    public function __construct(
        private ICommentRepository $repository,
    ) {
    }

    /**
     * Получить комментарий по ID.
     *
     * @param  int $id
     * @return ICommentResult
     */
    public function getById(int $id): ICommentResult
    {
        try {
            $comment = $this->repository->getById($id);
            if ($comment === null) {
                return CommentResult::failure(
                    new CommentManagementException("Комментарий с ID {$id} не найден")
                );
            }
            return CommentResult::success($comment);
        } catch (Throwable $e) {
            return CommentResult::failure($e);
        }
    }

    /**
     * Получить комментарии по ID лида.
     *
     * @param  int $leadId
     * @return IResult
     */
    public function getByLeadId(int $leadId): IResult
    {
        try {
            $comments = $this->repository->getByLeadId($leadId);
            return CommentResult::success($comments);
        } catch (Throwable $e) {
            return CommentResult::failure($e);
        }
    }

    /**
     * Получить комментарии по ID пользователя (userId).
     *
     * @param  int|null $userId
     * @return IResult
     */
    public function getByUserId(?int $userId): IResult
    {
        try {
            $comments = $this->repository->getByUserId($userId);
            return CommentResult::success($comments);
        } catch (Throwable $e) {
            return CommentResult::failure($e);
        }
    }

    /**
     * Получить комментарии по ID депозита.
     *
     * @param  int|null $depositId
     * @return IResult
     */
    public function getByDepositId(?int $depositId): IResult
    {
        try {
            $comments = $this->repository->getByDepositId($depositId);
            return CommentResult::success($comments);
        } catch (Throwable $e) {
            return CommentResult::failure($e);
        }
    }

    /**
     * Получить все комментарии.
     *
     * @return IResult
     */
    public function getAll(): IResult
    {
        try {
            $comments = $this->repository->getAll();
            return CommentResult::success($comments);
        } catch (Throwable $e) {
            return CommentResult::failure($e);
        }
    }

    /**
     * @template T
     * @param    callable(User): T $mapper
     * @return   IUserResult
     */
    public function executeAllMapped(callable $mapper): IResult
    {
        try {
            $users = $this->repository->getAll();
            $mapped = array_map($mapper, $users);

            return CommentResult::success($mapped);
        } catch (\Throwable $e) {
            return CommentResult::failure($e);
        }
    }
}
