<?php

namespace crm\src\Investments\InvStatus;

use crm\src\_common\interfaces\IValidation;
use Domain\Investment\DTOs\InvStatusInputDto;
use Domain\Investment\DTOs\DbInvStatusDto;
use crm\src\Investments\InvStatus\_mappers\InvStatusMapper;
use crm\src\Investments\InvStatus\_common\interfaces\IInvStatusRepository;
use crm\src\Investments\InvStatus\_common\interfaces\IInvStatusResult;
use crm\src\Investments\InvStatus\_common\adapters\InvStatusResult;
use crm\src\Investments\InvStatus\_exceptions\InvStatusException;
use Domain\Investment\InvStatus;

/**
 * Сервис управления инвестиционными статусами.
 */
class ManageInvStatus
{
    public function __construct(
        private IInvStatusRepository $repository,
        private IValidation $validator,
    ) {
    }

    /**
     * Создание нового статуса.
     *
     * @param  InvStatusInputDto $input
     * @return IInvStatusResult
     */
    public function create(InvStatusInputDto $input): IInvStatusResult
    {
        try {
            $validation = $this->validator->validate($input);
            if (!$validation->isValid()) {
                return InvStatusResult::failure(
                    new InvStatusException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $dto = InvStatusMapper::fromInputToDb($input);
            $result = $this->repository->save($dto);

            if (!$result->isSuccess()) {
                return InvStatusResult::failure(
                    $result->getError() ?? new InvStatusException("Ошибка при сохранении статуса")
                );
            }

            $entity = InvStatusMapper::fromDbToEntity($dto);
            return InvStatusResult::success($entity);
        } catch (\Throwable $e) {
            return InvStatusResult::failure($e);
        }
    }

    /**
     * Обновление статуса по ID.
     *
     * @param  InvStatusInputDto $input
     * @return IInvStatusResult
     */
    public function updateById(InvStatusInputDto $input): IInvStatusResult
    {
        try {
            if (!$input->id) {
                return InvStatusResult::failure(new InvStatusException("ID обязателен для обновления"));
            }

            $validation = $this->validator->validate($input);
            if (!$validation->isValid()) {
                return InvStatusResult::failure(
                    new InvStatusException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $updateData = InvStatusMapper::fromInputExtractFilledFields($input);
            $result = $this->repository->update($updateData);

            if (!$result->isSuccess()) {
                return InvStatusResult::failure(
                    $result->getError() ?? new InvStatusException("Ошибка при обновлении статуса")
                );
            }

            return InvStatusResult::success($updateData);
        } catch (\Throwable $e) {
            return InvStatusResult::failure($e);
        }
    }

    /**
     * Удаление статуса по ID.
     *
     * @param  int $id
     * @return IInvStatusResult
     */
    public function deleteById(int $id): IInvStatusResult
    {
        try {
            if ($id <= 0) {
                return InvStatusResult::failure(new InvStatusException("Некорректный ID для удаления"));
            }

            $result = $this->repository->deleteById($id);

            if (!$result->isSuccess()) {
                return InvStatusResult::failure(
                    $result->getError() ?? new InvStatusException("Ошибка при удалении статуса")
                );
            }

            return InvStatusResult::success($id);
        } catch (\Throwable $e) {
            return InvStatusResult::failure($e);
        }
    }
}
