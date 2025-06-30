<?php

namespace crm\src\components\CommentManagement;

use Throwable;
use crm\src\_common\interfaces\IValidation;
use crm\src\components\CommentManagement\_entities\Comment;
use crm\src\components\CommentManagement\_common\adapters\CommentResult;
use crm\src\components\CommentManagement\_common\interfaces\ICommentResult;
use crm\src\components\CommentManagement\_common\interfaces\ICommentRepository;

class CreateComment
{
    public function __construct(
        private ICommentRepository $commentRepository,
        private IValidation $validator,
    ) {
    }

    /**
     * Создаёт новый комментарий.
     *
     * Валидирует данные, сохраняет через репозиторий и возвращает результат.
     *
     * @param  Comment $comment
     * @return ICommentResult
     */
    public function execute(Comment $comment): ICommentResult
    {
        $validationResult = $this->validator->validate($comment);

        if (!$validationResult->isValid()) {
            return CommentResult::failure(
                new \Exception('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        try {
            $commentId = $this->commentRepository->save($comment);

            if ($commentId === null || $commentId <= 0) {
                return CommentResult::failure(new \Exception('Не удалось сохранить комментарий'));
            }

            $comment->id = $commentId;

            return CommentResult::success($comment);
        } catch (Throwable $e) {
            return CommentResult::failure($e);
        }
    }
}
