<?php

namespace crm\src\Investments\InvBalance;

use crm\src\_common\interfaces\IValidation;
use crm\src\Investments\InvBalance\_mappers\InvBalanceMapper;
use crm\src\Investments\InvBalance\_common\DTOs\InputInvBalanceDto;
use crm\src\Investments\InvBalance\_exceptions\InvBalanceException;
use crm\src\Investments\InvBalance\_common\interfaces\IInvBalanceResult;
use crm\src\Investments\InvBalance\_common\interfaces\IInvBalanceRepository;
use crm\src\Investments\InvBalance\_common\adapters\InvBalanceResult;

/**
 * Сервис управления инвестиционным балансом.
 */
class ManageInvBalance
{
    /**
     * @param IInvBalanceRepository $repository
     * @param IValidation $validator
     */
    public function __construct(
        private IInvBalanceRepository $repository,
        private IValidation $validator,
    ) {
    }

    /**
     * Создание нового баланса.
     *
     * @param  InputInvBalanceDto $input
     * @return IInvBalanceResult
     */
    public function create(InputInvBalanceDto $input): IInvBalanceResult
    {
        try {
            $validation = $this->validator->validate($input);
            if (!$validation->isValid()) {
                return InvBalanceResult::failure(
                    new InvBalanceException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $dto = InvBalanceMapper::fromInputToDb($input);
            $result = $this->repository->save($dto);

            if (!$result->isSuccess()) {
                return InvBalanceResult::failure(
                    $result->getError() ?? new InvBalanceException("Ошибка сохранения баланса")
                );
            }

            $entity = InvBalanceMapper::fromDbToEntity($dto);

            return InvBalanceResult::success($entity);
        } catch (\Throwable $e) {
            return InvBalanceResult::failure($e);
        }
    }

    /**
     * Обновление баланса по lead_uid.
     *
     * @param  InputInvBalanceDto $input
     * @return IInvBalanceResult
     */
    public function updateByLeadUid(InputInvBalanceDto $input): IInvBalanceResult
    {
        try {
            if (empty($input->leadUid)) {
                return InvBalanceResult::failure(new InvBalanceException("leadUid обязателен для обновления"));
            }

            $validation = $this->validator->validate($input);
            if (!$validation->isValid()) {
                return InvBalanceResult::failure(
                    new InvBalanceException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $updateData = InvBalanceMapper::fromInputExtractFilledFields($input);
            $result = $this->repository->update($updateData);

            if (!$result->isSuccess()) {
                return InvBalanceResult::failure(
                    $result->getError() ?? new InvBalanceException("Ошибка при обновлении баланса")
                );
            }

            return InvBalanceResult::success($updateData);
        } catch (\Throwable $e) {
            return InvBalanceResult::failure($e);
        }
    }

    /**
     * Удаление баланса по lead_uid.
     *
     * @param  string $leadUid
     * @return IInvBalanceResult
     */
    public function deleteByLeadUid(string $leadUid): IInvBalanceResult
    {
        try {
            if (empty($leadUid)) {
                return InvBalanceResult::failure(new InvBalanceException("leadUid обязателен для удаления"));
            }

            $result = $this->repository->deleteByLeadUid($leadUid);

            if (!$result->isSuccess()) {
                return InvBalanceResult::failure(
                    $result->getError() ?? new InvBalanceException("Ошибка при удалении баланса")
                );
            }

            return InvBalanceResult::success($leadUid);
        } catch (\Throwable $e) {
            return InvBalanceResult::failure($e);
        }
    }
}
