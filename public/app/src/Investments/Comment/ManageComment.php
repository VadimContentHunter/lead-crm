<?php

namespace crm\src\Investments\Comment;

use crm\src\_common\interfaces\IValidation;
use crm\src\Investments\Comment\_mappers\CommentMapper;
use crm\src\Investments\Comment\_common\DTOs\InvCommentInputDto;
use crm\src\Investments\Comment\_common\DTOs\DbInvCommentDto;
use crm\src\Investments\Comment\_common\adapters\CommentResult;
use crm\src\Investments\Comment\_exceptions\InvCommentException;
use crm\src\Investments\Comment\_common\interfaces\ICommentResult;
use crm\src\Investments\Comment\_common\interfaces\ICommentRepository;

class ManageComment
{
    public function __construct(
        private ICommentRepository $repository,
        private IValidation $validator,
    ) {
    }

    /**
     * Создание нового комментария.
     *
     * @param  InvCommentInputDto $input
     * @return ICommentResult
     */
    public function create(InvCommentInputDto $input): ICommentResult
    {
        try {
            $validation = $this->validator->validate($input);
            if (!$validation->isValid()) {
                return CommentResult::failure(
                    new InvCommentException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $dto = CommentMapper::fromInputToDb($input);
            $result = $this->repository->save($dto);

            if (!$result->isSuccess()) {
                return CommentResult::failure(
                    $result->getError() ?? new InvCommentException("Ошибка сохранения комментария")
                );
            }

            /**
             * @var DbInvCommentDto $saved
             */
            $saved = $dto;
            $entity = CommentMapper::fromDbToEntity($saved);

            return CommentResult::success($entity);
        } catch (\Throwable $e) {
            return CommentResult::failure($e);
        }
    }

    /**
     * Обновление комментария по ID.
     *
     * @param  InvCommentInputDto $input
     * @return ICommentResult
     */
    public function updateById(InvCommentInputDto $input): ICommentResult
    {
        try {
            if (empty($input->id)) {
                return CommentResult::failure(new InvCommentException("ID обязателен для обновления"));
            }

            $validation = $this->validator->validate($input, ignoreFields: ['leadUid']);
            if (!$validation->isValid()) {
                return CommentResult::failure(
                    new InvCommentException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $updateData = CommentMapper::fromInputExtractFilledFields($input);
            $result = $this->repository->update($updateData);

            if (!$result->isSuccess()) {
                return CommentResult::failure(
                    $result->getError() ?? new InvCommentException("Ошибка при обновлении комментария")
                );
            }

            return CommentResult::success($updateData);
        } catch (\Throwable $e) {
            return CommentResult::failure($e);
        }
    }

    /**
     * Удаление комментария по ID.
     *
     * @param  int $id
     * @return ICommentResult
     */
    public function deleteById(int $id): ICommentResult
    {
        try {
            if ($id <= 0) {
                return CommentResult::failure(new InvCommentException("Некорректный ID для удаления"));
            }

            $result = $this->repository->deleteById($id);

            if (!$result->isSuccess()) {
                return CommentResult::failure(
                    $result->getError() ?? new InvCommentException("Ошибка при удалении комментария")
                );
            }

            return CommentResult::success($id);
        } catch (\Throwable $e) {
            return CommentResult::failure($e);
        }
    }
}
