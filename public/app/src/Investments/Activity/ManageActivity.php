<?php

namespace crm\src\Investments\Activity;

use crm\src\_common\interfaces\IValidation;
use crm\src\Investments\Activity\_mappers\ActivityMapper;
use crm\src\Investments\Activity\_common\DTOs\ActivityInputDto;
use crm\src\Investments\Activity\_common\DTOs\DbActivityDto;
use crm\src\Investments\Activity\_common\adapters\ActivityResult;
use crm\src\Investments\Activity\_exceptions\InvActivityException;
use crm\src\Investments\Activity\_common\interfaces\IActivityResult;
use crm\src\Investments\Activity\_common\interfaces\IActivityRepository;
use crm\src\Investments\Activity\_entities\InvActivity;

/**
 * Сервис управления инвестиционными сделками.
 */
class ManageActivity
{
    /**
     * @param IActivityRepository $repository
     * @param IValidation $validator
     */
    public function __construct(
        private IActivityRepository $repository,
        private IValidation $validator,
    ) {
    }

    /**
     * Создание новой инвестиционной сделки.
     *
     * @param  ActivityInputDto $input
     * @return IActivityResult
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

            $dto = ActivityMapper::fromInputToDb($input);
            $result = $this->repository->save($dto);

            if (!$result->isSuccess()) {
                return ActivityResult::failure(
                    $result->getError() ?? new InvActivityException("Ошибка сохранения сделки")
                );
            }

            /**
             * @var DbActivityDto $saved
             */
            $saved = $dto;
            $entity = ActivityMapper::fromDbToEntity($saved);

            return ActivityResult::success($entity);
        } catch (\Throwable $e) {
            return ActivityResult::failure($e);
        }
    }

    /**
     * Обновление существующей сделки.
     *
     * @param  ActivityInputDto $input
     * @return IActivityResult
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
                return ActivityResult::failure(
                    $result->getError() ?? new InvActivityException("Ошибка при обновлении сделки")
                );
            }

            return ActivityResult::success($updateData);
        } catch (\Throwable $e) {
            return ActivityResult::failure($e);
        }
    }

    /**
     * Удаление сделки по ID.
     *
     * @param  int $id
     * @return IActivityResult
     */
    public function deleteById(int $id): IActivityResult
    {
        try {
            if ($id <= 0) {
                return ActivityResult::failure(new InvActivityException("Некорректный ID для удаления"));
            }

            $result = $this->repository->deleteById($id);

            if (!$result->isSuccess()) {
                return ActivityResult::failure(
                    $result->getError() ?? new InvActivityException("Ошибка при удалении сделки")
                );
            }

            return ActivityResult::success($id);
        } catch (\Throwable $e) {
            return ActivityResult::failure($e);
        }
    }
}
