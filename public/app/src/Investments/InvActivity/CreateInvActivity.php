<?php

namespace crm\src\Investments\InvActivity;

use DateTimeImmutable;
use crm\src\_common\interfaces\IValidation;
use crm\src\Investments\InvActivity\_entities\DealType;
use crm\src\Investments\InvActivity\CalculatePnlService;
use crm\src\Investments\InvActivity\_common\DTOs\InvActivityInputDto;
use crm\src\Investments\InvActivity\_exceptions\InvActivityException;
use crm\src\Investments\InvActivity\_common\mappers\InvActivityMapper;
use crm\src\Investments\InvActivity\_common\adapters\InvActivityResult;
use crm\src\Investments\InvActivity\_common\interfaces\IInvActivityRepository;

final class CreateInvActivity
{
    public function __construct(
        private IInvActivityRepository $repository,
        private IValidation $validator,
        private CalculatePnlService $pnlService,
    ) {
    }

    public function handle(InvActivityInputDto $input): InvActivityResult
    {
        try {
            $validation = $this->validator->validate($input);
            if (!$validation->isValid()) {
                return InvActivityResult::failure(
                    new InvActivityException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $input->InvActivityHash = InvActivityMapper::generateActivityHash($input);

            $this->prepareClosedStateIfNeeded($input);
            $this->failIfDuplicateHash($input->InvActivityHash);

            $dto = InvActivityMapper::fromInputToDb($input);
            $result = $this->repository->save($dto);

            if (!$result->isSuccess()) {
                return InvActivityResult::failure(
                    $result->getError() ?? new InvActivityException("Ошибка сохранения сделки")
                );
            }

            $dto->id = $result->getInt() ?? 0;
            $entity = InvActivityMapper::fromDbToEntity($dto);
            return InvActivityResult::success($entity);
        } catch (\Throwable $e) {
            return InvActivityResult::failure($e);
        }
    }

    private function prepareClosedStateIfNeeded(InvActivityInputDto $input): void
    {
        if (!DealType::equals($input->type, DealType::CLOSED)) {
            return;
        }

        if ($input->closePrice === null) {
            throw new InvActivityException("Цена закрытия обязательна для закрытой сделки");
        }

        $input->closeTime ??= (new DateTimeImmutable())->format('Y-m-d H:i:s');
        $input->result = $this->pnlService->calculateRawPnl($input);
    }

    private function failIfDuplicateHash(string $hash): void
    {
        if ($this->repository->getAllByColumnValues('activity_hash', [$hash])->count() > 0) {
            throw new InvActivityException('Такая сделка уже существует.');
        }
    }
}
