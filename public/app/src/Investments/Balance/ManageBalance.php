<?php

namespace crm\src\Investments\Balance;

use crm\src\_common\interfaces\IValidation;
use crm\src\Investments\Balance\_mappers\BalanceMapper;
use crm\src\Investments\Balance\_common\DTOs\InputInvBalanceDto;
use crm\src\Investments\Balance\_exceptions\InvBalanceException;
use crm\src\Investments\Balance\_common\interfaces\IBalanceResult;
use crm\src\Investments\Balance\_common\interfaces\IBalanceRepository;
use crm\src\Investments\Balance\_common\adapters\BalanceResult;

/**
 * Сервис управления инвестиционным балансом.
 */
class ManageBalance
{
    /**
     * @param IBalanceRepository $repository
     * @param IValidation $validator
     */
    public function __construct(
        private IBalanceRepository $repository,
        private IValidation $validator,
    ) {
    }

    /**
     * Создание нового баланса.
     *
     * @param  InputInvBalanceDto $input
     * @return IBalanceResult
     */
    public function create(InputInvBalanceDto $input): IBalanceResult
    {
        try {
            $validation = $this->validator->validate($input);
            if (!$validation->isValid()) {
                return BalanceResult::failure(
                    new InvBalanceException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $dto = BalanceMapper::fromInputToDb($input);
            $result = $this->repository->save($dto);

            if (!$result->isSuccess()) {
                return BalanceResult::failure(
                    $result->getError() ?? new InvBalanceException("Ошибка сохранения баланса")
                );
            }

            $entity = BalanceMapper::fromDbToEntity($dto);

            return BalanceResult::success($entity);
        } catch (\Throwable $e) {
            return BalanceResult::failure($e);
        }
    }

    /**
     * Обновление баланса по lead_uid.
     *
     * @param  InputInvBalanceDto $input
     * @return IBalanceResult
     */
    public function updateByLeadUid(InputInvBalanceDto $input): IBalanceResult
    {
        try {
            if (empty($input->leadUid)) {
                return BalanceResult::failure(new InvBalanceException("leadUid обязателен для обновления"));
            }

            $validation = $this->validator->validate($input);
            if (!$validation->isValid()) {
                return BalanceResult::failure(
                    new InvBalanceException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $updateData = BalanceMapper::fromInputExtractFilledFields($input);
            $result = $this->repository->update($updateData);

            if (!$result->isSuccess()) {
                return BalanceResult::failure(
                    $result->getError() ?? new InvBalanceException("Ошибка при обновлении баланса")
                );
            }

            return BalanceResult::success($updateData);
        } catch (\Throwable $e) {
            return BalanceResult::failure($e);
        }
    }

    /**
     * Удаление баланса по lead_uid.
     *
     * @param  string $leadUid
     * @return IBalanceResult
     */
    public function deleteByLeadUid(string $leadUid): IBalanceResult
    {
        try {
            if (empty($leadUid)) {
                return BalanceResult::failure(new InvBalanceException("leadUid обязателен для удаления"));
            }

            $result = $this->repository->deleteByLeadUid($leadUid);

            if (!$result->isSuccess()) {
                return BalanceResult::failure(
                    $result->getError() ?? new InvBalanceException("Ошибка при удалении баланса")
                );
            }

            return BalanceResult::success($leadUid);
        } catch (\Throwable $e) {
            return BalanceResult::failure($e);
        }
    }
}
