<?php

namespace crm\src\Investments\InvActivity;

use crm\src\_common\interfaces\IValidation;
use crm\src\Investments\InvActivity\_common\DTOs\InvActivityInputDto;
use crm\src\Investments\InvActivity\_exceptions\InvActivityException;
use crm\src\Investments\InvActivity\_common\mappers\InvActivityMapper;
use crm\src\Investments\InvActivity\_common\adapters\InvActivityResult;
use crm\src\Investments\InvActivity\_common\interfaces\IInvActivityRepository;

final class UpdateInvActivity
{
    public function __construct(
        private IInvActivityRepository $repository,
        private IValidation $validator
    ) {
    }

    public function handle(InvActivityInputDto $input): InvActivityResult
    {
        try {
            if (!$input->id) {
                return InvActivityResult::failure(new InvActivityException("ID обязателен для обновления"));
            }

            $validation = $this->validator->validate($input, ignoreFields: ['leadUid']);
            if (!$validation->isValid()) {
                return InvActivityResult::failure(
                    new InvActivityException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $updateData = InvActivityMapper::fromInputExtractFilledFields($input);
            $result = $this->repository->update($updateData);

            if (!$result->isSuccess()) {
                return InvActivityResult::failure(
                    $result->getError() ?? new InvActivityException("Ошибка при обновлении сделки")
                );
            }

            return InvActivityResult::success($updateData);
        } catch (\Throwable $e) {
            return InvActivityResult::failure($e);
        }
    }
}
