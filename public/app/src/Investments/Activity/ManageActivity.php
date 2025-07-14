<?php

namespace crm\src\Investments\Activity;

use crm\src\_common\interfaces\IValidation;
use crm\src\Investments\Activity\_mappers\ActivityMapper;
use crm\src\Investments\Activity\_common\DTOs\ActivityInputDto;
use crm\src\Investments\Activity\_common\adapters\ActivityResult;
use crm\src\Investments\Activity\_exceptions\InvActivityException;
use crm\src\Investments\Activity\_common\interfaces\IActivityResult;
use crm\src\Investments\Activity\_common\interfaces\IActivityRepository;

class ManageActivity
{
    public function __construct(
        private IActivityRepository $repository,
        private IValidation $validator,
    ) {
    }

    /**
     * Создание новой инвестиционной сделки.
     */
    public function create(ActivityInputDto $input): IActivityResult
    {
        try {
            $validation = $this->validator->validate($input);
            if (!$validation->isValid()) {
                return ActivityResult::failure(
                    new InvActivityException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $entity = ActivityMapper::fromInputToEntity($input);
            $result = $this->repository->save($entity);

            if (!$result->isSuccess()) {
                return ActivityResult::failure($result->getError() ?? new InvActivityException("Ошибка сохранения сделки"));
            }

            return ActivityResult::success($entity);
        } catch (\Throwable $e) {
            return ActivityResult::failure($e);
        }
    }

    /**
     * Обновление существующей сделки.
     */
    public function updateById(ActivityInputDto $input): IActivityResult
    {
        try {
            if (!$input->id) {
                return ActivityResult::failure(new InvActivityException("ID обязателен для обновления"));
            }

            $validation = $this->validator->validate($input, ignoreFields: ['leadUid']);
            if (!$validation->isValid()) {
                return ActivityResult::failure(
                    new InvActivityException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $updateData = ActivityMapper::fromInputExtractFilledFields($input);
            $result = $this->repository->update($updateData);

            if (!$result->isSuccess()) {
                return ActivityResult::failure($result->getError() ?? new InvActivityException("Ошибка при обновлении сделки"));
            }

            return ActivityResult::success($updateData);
        } catch (\Throwable $e) {
            return ActivityResult::failure($e);
        }
    }

    /**
     * Удаление сделки по ID.
     */
    public function deleteById(int $id): IActivityResult
    {
        try {
            if ($id <= 0) {
                return ActivityResult::failure(new InvActivityException("Некорректный ID для удаления"));
            }

            $result = $this->repository->deleteById($id);

            if (!$result->isSuccess()) {
                return ActivityResult::failure($result->getError() ?? new InvActivityException("Ошибка при удалении сделки"));
            }

            return ActivityResult::success($id);
        } catch (\Throwable $e) {
            return ActivityResult::failure($e);
        }
    }
}
