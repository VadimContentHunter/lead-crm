<?php

namespace crm\src\Investments\InvDeposit;

use crm\src\_common\interfaces\IValidation;
use crm\src\Investments\InvDeposit\_mappers\InvDepositMapper;
use crm\src\Investments\InvDeposit\_common\DTOs\InvDepositInputDto;
use crm\src\Investments\InvDeposit\_common\DTOs\DbInvDepositDto;
use crm\src\Investments\InvDeposit\_common\adapters\InvDepositResult;
use crm\src\Investments\InvDeposit\_exceptions\InvDepositException;
use crm\src\Investments\InvDeposit\_common\interfaces\IInvDepositResult;
use crm\src\Investments\InvDeposit\_common\interfaces\IInvDepositRepository;

/**
 * Сервис управления инвестиционными депозитами.
 */
class ManageInvDeposit
{
    public function __construct(
        private IInvDepositRepository $repository,
        private IValidation $validator,
    ) {
    }

    /**
     * Создание нового депозита.
     *
     * @param  InvDepositInputDto $input
     * @return IInvDepositResult
     */
    public function create(InvDepositInputDto $input): IInvDepositResult
    {
        try {
            $validation = $this->validator->validate($input);
            if (!$validation->isValid()) {
                return InvDepositResult::failure(
                    new InvDepositException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $dto = InvDepositMapper::fromInputToDb($input);
            $result = $this->repository->save($dto);

            if (!$result->isSuccess()) {
                return InvDepositResult::failure(
                    $result->getError() ?? new InvDepositException("Ошибка сохранения депозита")
                );
            }

            /**
 * @var DbInvDepositDto $saved
*/
            $saved = $dto;
            $entity = InvDepositMapper::fromDbToEntity($saved);

            return InvDepositResult::success($entity);
        } catch (\Throwable $e) {
            return InvDepositResult::failure($e);
        }
    }

    /**
     * Обновление депозита по ID.
     *
     * @param  InvDepositInputDto $input
     * @return IInvDepositResult
     */
    public function updateById(InvDepositInputDto $input): IInvDepositResult
    {
        try {
            if (!$input->id) {
                return InvDepositResult::failure(new InvDepositException("ID обязателен для обновления"));
            }

            $validation = $this->validator->validate($input, ignoreFields: ['uid']);
            if (!$validation->isValid()) {
                return InvDepositResult::failure(
                    new InvDepositException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $updateData = InvDepositMapper::fromInputExtractFilledFields($input);
            $result = $this->repository->update($updateData);

            if (!$result->isSuccess()) {
                return InvDepositResult::failure(
                    $result->getError() ?? new InvDepositException("Ошибка при обновлении депозита")
                );
            }

            return InvDepositResult::success($updateData);
        } catch (\Throwable $e) {
            return InvDepositResult::failure($e);
        }
    }

    /**
     * Удаление депозита по ID.
     *
     * @param  int $id
     * @return IInvDepositResult
     */
    public function deleteById(int $id): IInvDepositResult
    {
        try {
            if ($id <= 0) {
                return InvDepositResult::failure(new InvDepositException("Некорректный ID для удаления"));
            }

            $result = $this->repository->deleteById($id);

            if (!$result->isSuccess()) {
                return InvDepositResult::failure(
                    $result->getError() ?? new InvDepositException("Ошибка при удалении депозита")
                );
            }

            return InvDepositResult::success($id);
        } catch (\Throwable $e) {
            return InvDepositResult::failure($e);
        }
    }
}
