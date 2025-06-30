<?php

namespace crm\src\components\CommentManagement;

use Throwable;
use crm\src\_common\interfaces\IValidation;
use crm\src\components\CommentManagement\_common\adapters\CommentResult;
use crm\src\components\CommentManagement\_common\interfaces\ICommentResult;
use crm\src\components\CommentManagement\_common\interfaces\ICommentRepository;
use crm\src\components\CommentManagement\_exceptions\CommentManagementException;

class DeleteComment
{
    public function __construct(
        private ICommentRepository $repository,
        private IValidation $validator,
    ) {
    }

    /**
     * Удалить комментарий по ID
     *
     * @param  int $id
     * @return ICommentResult
     */
    public function executeById(int $id): ICommentResult
    {
        $validationResult = $this->validator->validateArray(['id' => $id]);

        if (!$validationResult->isValid()) {
            return CommentResult::failure(
                new CommentManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        try {
            $deletedId = $this->repository->deleteById($id);

            if ($deletedId === null) {
                return CommentResult::failure(
                    new CommentManagementException("Комментарий с ID {$id} не найден или не удалён")
                );
            }

            return CommentResult::success($deletedId);
        } catch (Throwable $e) {
            return CommentResult::failure($e);
        }
    }

    /**
     * Удалить комментарии по leadId
     *
     * @param  int $leadId
     * @return ICommentResult
     */
    public function executeByLeadId(int $leadId): ICommentResult
    {
        $validationResult = $this->validator->validateArray(['leadId' => $leadId]);

        if (!$validationResult->isValid()) {
            return CommentResult::failure(
                new CommentManagementException('Ошибка валидации: ' . implode('; ', $validationResult->getErrors()))
            );
        }

        try {
            $deletedCount = $this->repository->deleteByLeadId($leadId);

            if ($deletedCount === null) {
                return CommentResult::failure(
                    new CommentManagementException("Комментарии с leadId {$leadId} не найдены или не удалены")
                );
            }

            return CommentResult::success($deletedCount);
        } catch (Throwable $e) {
            return CommentResult::failure($e);
        }
    }
}
