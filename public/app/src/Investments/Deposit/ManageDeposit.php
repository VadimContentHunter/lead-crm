<?php

namespace crm\src\Investments\Deposit;

use crm\src\_common\interfaces\IValidation;
use crm\src\Investments\Deposit\_mappers\InvDepositMapper;
use crm\src\Investments\Deposit\_common\DTOs\InvDepositInputDto;
use crm\src\Investments\Deposit\_common\DTOs\DbInvDepositDto;
use crm\src\Investments\Deposit\_common\adapters\DepositResult;
use crm\src\Investments\Deposit\_exceptions\InvDepositException;
use crm\src\Investments\Deposit\_common\interfaces\IDepositResult;
use crm\src\Investments\Deposit\_common\interfaces\IDepositRepository;

/**
 * Сервис управления инвестиционными депозитами.
 */
class ManageDeposit
{
    public function __construct(
        private IDepositRepository $repository,
        private IValidation $validator,
    ) {
    }

    /**
     * Создание нового депозита.
     *
     * @param  InvDepositInputDto $input
     * @return IDepositResult
     */
    public function create(InvDepositInputDto $input): IDepositResult
    {
        try {
            $validation = $this->validator->validate($input);
            if (!$validation->isValid()) {
                return DepositResult::failure(
                    new InvDepositException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $dto = InvDepositMapper::fromInputToDb($input);
            $result = $this->repository->save($dto);

            if (!$result->isSuccess()) {
                return DepositResult::failure(
                    $result->getError() ?? new InvDepositException("Ошибка сохранения депозита")
                );
            }

            /**
 * @var DbInvDepositDto $saved
*/
            $saved = $dto;
            $entity = InvDepositMapper::fromDbToEntity($saved);

            return DepositResult::success($entity);
        } catch (\Throwable $e) {
            return DepositResult::failure($e);
        }
    }

    /**
     * Обновление депозита по ID.
     *
     * @param  InvDepositInputDto $input
     * @return IDepositResult
     */
    public function updateById(InvDepositInputDto $input): IDepositResult
    {
        try {
            if (!$input->id) {
                return DepositResult::failure(new InvDepositException("ID обязателен для обновления"));
            }

            $validation = $this->validator->validate($input, ignoreFields: ['uid']);
            if (!$validation->isValid()) {
                return DepositResult::failure(
                    new InvDepositException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $updateData = InvDepositMapper::fromInputExtractFilledFields($input);
            $result = $this->repository->update($updateData);

            if (!$result->isSuccess()) {
                return DepositResult::failure(
                    $result->getError() ?? new InvDepositException("Ошибка при обновлении депозита")
                );
            }

            return DepositResult::success($updateData);
        } catch (\Throwable $e) {
            return DepositResult::failure($e);
        }
    }

    /**
     * Удаление депозита по ID.
     *
     * @param  int $id
     * @return IDepositResult
     */
    public function deleteById(int $id): IDepositResult
    {
        try {
            if ($id <= 0) {
                return DepositResult::failure(new InvDepositException("Некорректный ID для удаления"));
            }

            $result = $this->repository->deleteById($id);

            if (!$result->isSuccess()) {
                return DepositResult::failure(
                    $result->getError() ?? new InvDepositException("Ошибка при удалении депозита")
                );
            }

            return DepositResult::success($id);
        } catch (\Throwable $e) {
            return DepositResult::failure($e);
        }
    }
}
