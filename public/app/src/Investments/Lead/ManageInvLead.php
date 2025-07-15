<?php

namespace crm\src\Investments\Lead;

use crm\src\_common\interfaces\IValidation;
use crm\src\Investments\Lead\_dto\InvLeadInputDto;
use crm\src\Investments\Lead\_mappers\InvLeadMapper;
use crm\src\Investments\Lead\_common\interfaces\IInvLeadRepository;
use crm\src\Investments\Lead\_common\interfaces\IInvLeadResult;
use crm\src\Investments\Lead\_common\adapters\InvLeadResult;
use crm\src\Investments\Lead\_exceptions\InvLeadException;

/**
 * Сервис управления инвестиционными лидами.
 */
class ManageInvLead
{
    public function __construct(
        private IInvLeadRepository $repository,
        private IValidation $validator,
    ) {
    }

    /**
     * Создание нового лида.
     */
    public function create(InvLeadInputDto $input): IInvLeadResult
    {
        try {
            if (!$input->uid) {
                return InvLeadResult::failure(new InvLeadException("UID обязателен для создания лида"));
            }

            $validation = $this->validator->validate($input);
            if (!$validation->isValid()) {
                return InvLeadResult::failure(
                    new InvLeadException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $dto = InvLeadMapper::fromInputToDb($input, $input->uid);
            $result = $this->repository->save($dto);

            if (!$result->isSuccess()) {
                return InvLeadResult::failure(
                    $result->getError() ?? new InvLeadException("Ошибка при сохранении лида")
                );
            }

            return InvLeadResult::success($input->uid);
        } catch (\Throwable $e) {
            return InvLeadResult::failure($e);
        }
    }

    /**
     * Обновление лида по UID.
     */
    public function updateByUid(InvLeadInputDto $input): IInvLeadResult
    {
        try {
            if (!$input->uid) {
                return InvLeadResult::failure(new InvLeadException("UID обязателен для обновления"));
            }

            $validation = $this->validator->validate($input);
            if (!$validation->isValid()) {
                return InvLeadResult::failure(
                    new InvLeadException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $updateData = InvLeadMapper::fromInputExtractFilledFields($input);
            $updateData['uid'] = $input->uid;

            $result = $this->repository->update($updateData);

            if (!$result->isSuccess()) {
                return InvLeadResult::failure(
                    $result->getError() ?? new InvLeadException("Ошибка при обновлении лида")
                );
            }

            return InvLeadResult::success($input->uid);
        } catch (\Throwable $e) {
            return InvLeadResult::failure($e);
        }
    }

    /**
     * Удаление лида по UID.
     */
    public function deleteByUid(string $uid): IInvLeadResult
    {
        try {
            if (!$uid || strlen($uid) < 3) {
                return InvLeadResult::failure(new InvLeadException("Некорректный UID для удаления"));
            }

            $result = $this->repository->deleteByUid($uid);

            if (!$result->isSuccess()) {
                return InvLeadResult::failure(
                    $result->getError() ?? new InvLeadException("Ошибка при удалении лида")
                );
            }

            return InvLeadResult::success($uid);
        } catch (\Throwable $e) {
            return InvLeadResult::failure($e);
        }
    }
}
