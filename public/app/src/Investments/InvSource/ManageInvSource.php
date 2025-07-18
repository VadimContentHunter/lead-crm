<?php

namespace crm\src\Investments\InvSource;

use crm\src\_common\interfaces\IValidation;
use crm\src\Investments\InvSource\_common\DTOs\InvSourceInputDto;
use crm\src\Investments\InvSource\_common\DTOs\DbInvSourceDto;
use crm\src\Investments\InvSource\_common\mappers\InvSourceMapper;
use crm\src\Investments\InvSource\_common\interfaces\IInvSourceRepository;
use crm\src\Investments\InvSource\_common\interfaces\IInvSourceResult;
use crm\src\Investments\InvSource\_common\adapters\InvSourceResult;
use crm\src\Investments\InvSource\_exceptions\InvSourceException;
use crm\src\Investments\InvSource\_entities\InvSource;

/**
 * Сервис управления инвестиционными источниками.
 */
class ManageInvSource
{
    public function __construct(
        private IInvSourceRepository $repository,
        private IValidation $validator,
    ) {
    }

    /**
     * Создание нового источника.
     *
     * @param  InvSourceInputDto $input
     * @return IInvSourceResult
     */
    public function create(InvSourceInputDto $input): IInvSourceResult
    {
        try {
            $validation = $this->validator->validate($input);
            if (!$validation->isValid()) {
                return InvSourceResult::failure(
                    new InvSourceException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $dto = InvSourceMapper::fromInputToDb($input);
            $result = $this->repository->save($dto);

            if (!$result->isSuccess()) {
                return InvSourceResult::failure(
                    $result->getError() ?? new InvSourceException("Ошибка при сохранении источника")
                );
            }

            $dto->id = $result->getInt() ?? 0;
            $entity = InvSourceMapper::fromDbToEntity($dto);
            return InvSourceResult::success($entity);
        } catch (\Throwable $e) {
            return InvSourceResult::failure($e);
        }
    }

    /**
     * Обновление источника по ID.
     *
     * @param  InvSourceInputDto $input
     * @return IInvSourceResult
     */
    public function updateById(InvSourceInputDto $input): IInvSourceResult
    {
        try {
            if (!$input->id) {
                return InvSourceResult::failure(new InvSourceException("ID обязателен для обновления"));
            }

            $validation = $this->validator->validate($input);
            if (!$validation->isValid()) {
                return InvSourceResult::failure(
                    new InvSourceException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $updateData = InvSourceMapper::fromInputExtractFilledFields($input);
            $result = $this->repository->update($updateData);

            if (!$result->isSuccess()) {
                return InvSourceResult::failure(
                    $result->getError() ?? new InvSourceException("Ошибка при обновлении источника")
                );
            }

            return InvSourceResult::success($updateData);
        } catch (\Throwable $e) {
            return InvSourceResult::failure($e);
        }
    }

    /**
     * Удаление источника по ID.
     *
     * @param  int $id
     * @return IInvSourceResult
     */
    public function deleteById(int $id): IInvSourceResult
    {
        try {
            if ($id <= 0) {
                return InvSourceResult::failure(new InvSourceException("Некорректный ID для удаления"));
            }

            $result = $this->repository->deleteById($id);

            if (!$result->isSuccess()) {
                return InvSourceResult::failure(
                    $result->getError() ?? new InvSourceException("Ошибка при удалении источника")
                );
            }

            return InvSourceResult::success($id);
        } catch (\Throwable $e) {
            return InvSourceResult::failure($e);
        }
    }
}
