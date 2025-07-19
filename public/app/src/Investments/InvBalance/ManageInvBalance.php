<?php

namespace crm\src\Investments\InvBalance;

use crm\src\_common\interfaces\IValidation;
use crm\src\Investments\InvBalance\_common\mappers\InvBalanceMapper;
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

            $thisBalance = $this->repository->getByLeadUid($input->leadUid);
            if (!$thisBalance->isSuccess()) {
                return InvBalanceResult::failure(
                    $thisBalance->getError() ?? new InvBalanceException("Баланс создан, но не удалось получить его данные")
                );
            }

            return InvBalanceResult::success($thisBalance->getData());
        } catch (\Throwable $e) {
            return InvBalanceResult::failure($e);
        }
    }

    public function createOrUpdateInvBalance(array $data): IInvBalanceResult
    {
        $balanceRes = $this->repository->getByLeadUid(InvBalanceMapper::fromArrayToInput($data)->leadUid)->first();
        if ($balanceRes->isSuccess()) {
            return $this->updateByLeadUid(InvBalanceMapper::fromArrayToInput($data));
        }
        return $this->create(InvBalanceMapper::fromArrayToInput($data));
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

            $thisBalance = $this->repository->getByLeadUid($input->leadUid);
            if (!$thisBalance->isSuccess()) {
                return InvBalanceResult::failure(
                    $thisBalance->getError() ?? new InvBalanceException("Баланс обновлен, но не удалось получить его данные")
                );
            }

            return InvBalanceResult::success($thisBalance->getData());
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
