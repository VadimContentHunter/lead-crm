<?php

namespace crm\src\Investments\Status;

use crm\src\_common\interfaces\IValidation;
use Domain\Investment\DTOs\InvStatusInputDto;
use Domain\Investment\DTOs\DbInvStatusDto;
use crm\src\Investments\Status\_mappers\StatusMapper;
use crm\src\Investments\Status\_common\interfaces\IStatusRepository;
use crm\src\Investments\Status\_common\interfaces\IStatusResult;
use crm\src\Investments\Status\_common\adapters\StatusResult;
use crm\src\Investments\Status\_exceptions\InvStatusException;
use Domain\Investment\InvStatus;

/**
 * Сервис управления инвестиционными статусами.
 */
class ManageStatus
{
    public function __construct(
        private IStatusRepository $repository,
        private IValidation $validator,
    ) {
    }

    /**
     * Создание нового статуса.
     *
     * @param  InvStatusInputDto $input
     * @return IStatusResult
     */
    public function create(InvStatusInputDto $input): IStatusResult
    {
        try {
            $validation = $this->validator->validate($input);
            if (!$validation->isValid()) {
                return StatusResult::failure(
                    new InvStatusException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $dto = StatusMapper::fromInputToDb($input);
            $result = $this->repository->save($dto);

            if (!$result->isSuccess()) {
                return StatusResult::failure(
                    $result->getError() ?? new InvStatusException("Ошибка при сохранении статуса")
                );
            }

            $entity = StatusMapper::fromDbToEntity($dto);
            return StatusResult::success($entity);
        } catch (\Throwable $e) {
            return StatusResult::failure($e);
        }
    }

    /**
     * Обновление статуса по ID.
     *
     * @param  InvStatusInputDto $input
     * @return IStatusResult
     */
    public function updateById(InvStatusInputDto $input): IStatusResult
    {
        try {
            if (!$input->id) {
                return StatusResult::failure(new InvStatusException("ID обязателен для обновления"));
            }

            $validation = $this->validator->validate($input);
            if (!$validation->isValid()) {
                return StatusResult::failure(
                    new InvStatusException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $updateData = StatusMapper::fromInputExtractFilledFields($input);
            $result = $this->repository->update($updateData);

            if (!$result->isSuccess()) {
                return StatusResult::failure(
                    $result->getError() ?? new InvStatusException("Ошибка при обновлении статуса")
                );
            }

            return StatusResult::success($updateData);
        } catch (\Throwable $e) {
            return StatusResult::failure($e);
        }
    }

    /**
     * Удаление статуса по ID.
     *
     * @param  int $id
     * @return IStatusResult
     */
    public function deleteById(int $id): IStatusResult
    {
        try {
            if ($id <= 0) {
                return StatusResult::failure(new InvStatusException("Некорректный ID для удаления"));
            }

            $result = $this->repository->deleteById($id);

            if (!$result->isSuccess()) {
                return StatusResult::failure(
                    $result->getError() ?? new InvStatusException("Ошибка при удалении статуса")
                );
            }

            return StatusResult::success($id);
        } catch (\Throwable $e) {
            return StatusResult::failure($e);
        }
    }
}
