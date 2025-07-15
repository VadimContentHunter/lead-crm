<?php

namespace crm\src\Investments\InvComment;

use crm\src\_common\interfaces\IValidation;
use crm\src\Investments\InvComment\_mappers\InvCommentMapper;
use crm\src\Investments\InvComment\_common\DTOs\InvCommentInputDto;
use crm\src\Investments\InvComment\_common\DTOs\DbInvCommentDto;
use crm\src\Investments\InvComment\_common\adapters\InvCommentResult;
use crm\src\Investments\InvComment\_exceptions\InvCommentException;
use crm\src\Investments\InvComment\_common\interfaces\IInvCommentResult;
use crm\src\Investments\InvComment\_common\interfaces\IInvCommentRepository;

class ManageInvComment
{
    public function __construct(
        private IInvCommentRepository $repository,
        private IValidation $validator,
    ) {
    }

    /**
     * Создание нового комментария.
     *
     * @param  InvCommentInputDto $input
     * @return IInvCommentResult
     */
    public function create(InvCommentInputDto $input): IInvCommentResult
    {
        try {
            $validation = $this->validator->validate($input);
            if (!$validation->isValid()) {
                return InvCommentResult::failure(
                    new InvCommentException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $dto = InvCommentMapper::fromInputToDb($input);
            $result = $this->repository->save($dto);

            if (!$result->isSuccess()) {
                return InvCommentResult::failure(
                    $result->getError() ?? new InvCommentException("Ошибка сохранения комментария")
                );
            }

            /**
             * @var DbInvCommentDto $saved
             */
            $saved = $dto;
            $entity = InvCommentMapper::fromDbToEntity($saved);

            return InvCommentResult::success($entity);
        } catch (\Throwable $e) {
            return InvCommentResult::failure($e);
        }
    }

    /**
     * Обновление комментария по ID.
     *
     * @param  InvCommentInputDto $input
     * @return IInvCommentResult
     */
    public function updateById(InvCommentInputDto $input): IInvCommentResult
    {
        try {
            if (empty($input->id)) {
                return InvCommentResult::failure(new InvCommentException("ID обязателен для обновления"));
            }

            $validation = $this->validator->validate($input, ignoreFields: ['leadUid']);
            if (!$validation->isValid()) {
                return InvCommentResult::failure(
                    new InvCommentException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $updateData = InvCommentMapper::fromInputExtractFilledFields($input);
            $result = $this->repository->update($updateData);

            if (!$result->isSuccess()) {
                return InvCommentResult::failure(
                    $result->getError() ?? new InvCommentException("Ошибка при обновлении комментария")
                );
            }

            return InvCommentResult::success($updateData);
        } catch (\Throwable $e) {
            return InvCommentResult::failure($e);
        }
    }

    /**
     * Удаление комментария по ID.
     *
     * @param  int $id
     * @return IInvCommentResult
     */
    public function deleteById(int $id): IInvCommentResult
    {
        try {
            if ($id <= 0) {
                return InvCommentResult::failure(new InvCommentException("Некорректный ID для удаления"));
            }

            $result = $this->repository->deleteById($id);

            if (!$result->isSuccess()) {
                return InvCommentResult::failure(
                    $result->getError() ?? new InvCommentException("Ошибка при удалении комментария")
                );
            }

            return InvCommentResult::success($id);
        } catch (\Throwable $e) {
            return InvCommentResult::failure($e);
        }
    }
}
