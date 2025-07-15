<?php

namespace crm\src\Investments\InvActivity;

use crm\src\_common\interfaces\IValidation;
use crm\src\Investments\InvActivity\_mappers\InvActivityMapper;
use crm\src\Investments\InvActivity\_common\DTOs\InvActivityInputDto;
use crm\src\Investments\InvActivity\_common\DTOs\DbInvActivityDto;
use crm\src\Investments\InvActivity\_common\adapters\InvActivityResult;
use crm\src\Investments\InvActivity\_exceptions\InvInvActivityException;
use crm\src\Investments\InvActivity\_common\interfaces\IInvActivityResult;
use crm\src\Investments\InvActivity\_common\interfaces\IInvActivityRepository;
use crm\src\Investments\InvActivity\_entities\InvInvActivity;

/**
 * Сервис управления инвестиционными сделками.
 */
class ManageInvActivity
{
    /**
     * @param IInvActivityRepository $repository
     * @param IValidation $validator
     */
    public function __construct(
        private IInvActivityRepository $repository,
        private IValidation $validator,
    ) {
    }

    /**
     * Создание новой инвестиционной сделки.
     *
     * @param  InvActivityInputDto $input
     * @return IInvActivityResult
     */
    public function create(InvActivityInputDto $input): IInvActivityResult
    {
        try {
            $validation = $this->validator->validate($input);
            if (!$validation->isValid()) {
                return InvActivityResult::failure(
                    new InvInvActivityException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $dto = InvActivityMapper::fromInputToDb($input);
            $result = $this->repository->save($dto);

            if (!$result->isSuccess()) {
                return InvActivityResult::failure(
                    $result->getError() ?? new InvInvActivityException("Ошибка сохранения сделки")
                );
            }

            /**
             * @var DbInvActivityDto $saved
             */
            $saved = $dto;
            $entity = InvActivityMapper::fromDbToEntity($saved);

            return InvActivityResult::success($entity);
        } catch (\Throwable $e) {
            return InvActivityResult::failure($e);
        }
    }

    /**
     * Обновление существующей сделки.
     *
     * @param  InvActivityInputDto $input
     * @return IInvActivityResult
     */
    public function updateById(InvActivityInputDto $input): IInvActivityResult
    {
        try {
            if (!$input->id) {
                return InvActivityResult::failure(new InvInvActivityException("ID обязателен для обновления"));
            }

            $validation = $this->validator->validate($input, ignoreFields: ['leadUid']);
            if (!$validation->isValid()) {
                return InvActivityResult::failure(
                    new InvInvActivityException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $updateData = InvActivityMapper::fromInputExtractFilledFields($input);
            $result = $this->repository->update($updateData);

            if (!$result->isSuccess()) {
                return InvActivityResult::failure(
                    $result->getError() ?? new InvInvActivityException("Ошибка при обновлении сделки")
                );
            }

            return InvActivityResult::success($updateData);
        } catch (\Throwable $e) {
            return InvActivityResult::failure($e);
        }
    }

    /**
     * Удаление сделки по ID.
     *
     * @param  int $id
     * @return IInvActivityResult
     */
    public function deleteById(int $id): IInvActivityResult
    {
        try {
            if ($id <= 0) {
                return InvActivityResult::failure(new InvInvActivityException("Некорректный ID для удаления"));
            }

            $result = $this->repository->deleteById($id);

            if (!$result->isSuccess()) {
                return InvActivityResult::failure(
                    $result->getError() ?? new InvInvActivityException("Ошибка при удалении сделки")
                );
            }

            return InvActivityResult::success($id);
        } catch (\Throwable $e) {
            return InvActivityResult::failure($e);
        }
    }
}
