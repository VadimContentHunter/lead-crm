<?php

namespace crm\src\Investments\Source;

use crm\src\_common\interfaces\IValidation;
use crm\src\Investments\Source\_common\DTOs\InvSourceInputDto;
use crm\src\Investments\Source\_common\DTOs\DbInvSourceDto;
use crm\src\Investments\Source\_mappers\SourceMapper;
use crm\src\Investments\Source\_common\interfaces\ISourceRepository;
use crm\src\Investments\Source\_common\interfaces\ISourceResult;
use crm\src\Investments\Source\_common\adapters\SourceResult;
use crm\src\Investments\Source\_exceptions\InvSourceException;
use crm\src\Investments\Source\_entities\InvSource;

/**
 * Сервис управления инвестиционными источниками.
 */
class ManageSource
{
    public function __construct(
        private ISourceRepository $repository,
        private IValidation $validator,
    ) {
    }

    /**
     * Создание нового источника.
     *
     * @param  InvSourceInputDto $input
     * @return ISourceResult
     */
    public function create(InvSourceInputDto $input): ISourceResult
    {
        try {
            $validation = $this->validator->validate($input);
            if (!$validation->isValid()) {
                return SourceResult::failure(
                    new InvSourceException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $dto = SourceMapper::fromInputToDb($input);
            $result = $this->repository->save($dto);

            if (!$result->isSuccess()) {
                return SourceResult::failure(
                    $result->getError() ?? new InvSourceException("Ошибка при сохранении источника")
                );
            }

            $entity = SourceMapper::fromDbToEntity($dto);
            return SourceResult::success($entity);
        } catch (\Throwable $e) {
            return SourceResult::failure($e);
        }
    }

    /**
     * Обновление источника по ID.
     *
     * @param  InvSourceInputDto $input
     * @return ISourceResult
     */
    public function updateById(InvSourceInputDto $input): ISourceResult
    {
        try {
            if (!$input->id) {
                return SourceResult::failure(new InvSourceException("ID обязателен для обновления"));
            }

            $validation = $this->validator->validate($input);
            if (!$validation->isValid()) {
                return SourceResult::failure(
                    new InvSourceException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $updateData = SourceMapper::fromInputExtractFilledFields($input);
            $result = $this->repository->update($updateData);

            if (!$result->isSuccess()) {
                return SourceResult::failure(
                    $result->getError() ?? new InvSourceException("Ошибка при обновлении источника")
                );
            }

            return SourceResult::success($updateData);
        } catch (\Throwable $e) {
            return SourceResult::failure($e);
        }
    }

    /**
     * Удаление источника по ID.
     *
     * @param  int $id
     * @return ISourceResult
     */
    public function deleteById(int $id): ISourceResult
    {
        try {
            if ($id <= 0) {
                return SourceResult::failure(new InvSourceException("Некорректный ID для удаления"));
            }

            $result = $this->repository->deleteById($id);

            if (!$result->isSuccess()) {
                return SourceResult::failure(
                    $result->getError() ?? new InvSourceException("Ошибка при удалении источника")
                );
            }

            return SourceResult::success($id);
        } catch (\Throwable $e) {
            return SourceResult::failure($e);
        }
    }
}
