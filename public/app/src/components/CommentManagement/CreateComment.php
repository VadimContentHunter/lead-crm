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
     * Генерирует строку комментария в консольном формате.
     *
     * @param  Comment   $comment
     * @param  string|null $userLabel Дополнительное имя пользователя (login / name)
     * @return string
     */
    public static function formatConsole(Comment $comment, ?string $userLabel = null): string
    {
        $date = $comment->createdAt?->format('Y-m-d H:i:s') ?? date('Y-m-d H:i:s');
        $userId = $comment->userId ?? 'system';

        $userPart = $userLabel ? " [$userId - $userLabel]" : " [$userId]";

        return sprintf("[%s]%s %s", $date, $userPart, $comment->comment);
    }

    /**
     * Создаёт новый комментарий.
     *
     * Валидирует данные, сохраняет через репозиторий и возвращает результат.
     *
     * @param  Comment $comment
     * @return ICommentResult
     */
    public function execute(Comment $comment, ?string $userLabel = null): ICommentResult
    {
        $validationResult = $this->validator->validate($comment);

        if (!$validationResult->isValid()) {
            return CommentResult::failure(
                new \Exception('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        try {
            $comment->createdAt = new \DateTime();
            $comment->comment = self::formatConsole($comment, $userLabel);
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
