<?php

namespace crm\src\components\CommentManagement;

use Throwable;
use crm\src\components\CommentManagement\_entities\Comment;
use crm\src\components\CommentManagement\_common\interfaces\ICommentRepository;
use crm\src\components\CommentManagement\_common\interfaces\ICommentResult;
use crm\src\components\CommentManagement\_common\adapters\CommentResult;
use crm\src\components\CommentManagement\_exceptions\CommentManagementException;
use crm\src\_common\interfaces\IValidation;

class UpdateComment
{
    public function __construct(
        private ICommentRepository $repository,
        private IValidation $validator,
    ) {
    }

    /**
     * Обновляет комментарий.
     *
     * @param  Comment $comment Обновляемый объект комментария.
     * @return ICommentResult Результат операции обновления.
     */
    public function execute(Comment $comment): ICommentResult
    {
        $validationResult = $this->validator->validate($comment);
        if (!$validationResult->isValid()) {
            return CommentResult::failure(
                new CommentManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        try {
            $updatedId = $this->repository->update($comment);
            if ($updatedId === null || $updatedId <= 0) {
                return CommentResult::failure(
                    new CommentManagementException('Не удалось обновить комментарий')
                );
            }

            return CommentResult::success($comment);
        } catch (Throwable $e) {
            return CommentResult::failure($e);
        }
    }
}
