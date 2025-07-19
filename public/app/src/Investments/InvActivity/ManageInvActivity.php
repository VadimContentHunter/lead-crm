<?php

namespace crm\src\Investments\InvActivity;

use DateTimeImmutable;
use crm\src\_common\interfaces\IValidation;
use crm\src\Investments\InvActivity\_entities\DealType;
use crm\src\Investments\InvActivity\_entities\InvActivity;
use crm\src\Investments\InvActivity\_entities\DealDirection;
use crm\src\Investments\InvActivity\_common\DTOs\DbInvActivityDto;
use crm\src\Investments\InvActivity\_common\DTOs\InvActivityInputDto;
use crm\src\Investments\InvActivity\_exceptions\InvActivityException;
use crm\src\Investments\InvActivity\_common\mappers\InvActivityMapper;
use crm\src\Investments\InvActivity\_common\adapters\InvActivityResult;
use crm\src\Investments\InvActivity\_common\interfaces\IInvActivityResult;
use crm\src\Investments\InvActivity\_common\interfaces\IInvActivityRepository;

/**
 * Сервис управления инвестиционными сделками.
 */
class ManageInvActivity
{
    /**
     * @param IInvActivityRepository $repository
     * @param IValidation $validator
     */
    public function __construct(
        private IInvActivityRepository $repository,
        private IValidation $validator,
    ) {
    }

    /**
     * Вычисляет результат сделки (прибыль или убыток).
     *
     * @param  InvActivity $activity
     * @return float
     */
    private static function calculateResult(InvActivity|InvActivityInputDto $activity): float
    {
        if ($activity instanceof InvActivityInputDto) {
            $activity = InvActivityMapper::fromInputToEntity($activity);
        }

        if ($activity->closePrice === null) {
            throw new \LogicException("Цена закрытия не задана");
        }

        $delta = $activity->closePrice - $activity->openPrice;
        $multiplier = $activity->direction === DealDirection::SHORT ? -1 : 1;

        return $delta * $activity->amount * $multiplier;
    }

    public function closeDeal(InvActivity $invActivity, ?DateTimeImmutable $closeTime = null, ?float $closePrice = null): IInvActivityResult
    {
        if (DealType::equals($invActivity->type, DealType::CLOSED)) {
            throw new \LogicException("Сделка уже закрыта");
        }

        $invActivity->type = DealType::CLOSED;
        $invActivity->closeTime = $closeTime ?? new DateTimeImmutable();
        $invActivity->closePrice ??= $closePrice;

        if ($invActivity->closePrice === null) {
            return InvActivityResult::failure(
                new InvActivityException("Цена закрытия обязательна для закрытия сделки")
            );
        }

        $invActivity->result = self::calculateResult($invActivity);

        $dbDto = InvActivityMapper::fromEntityToDb($invActivity);
        $result = $this->repository->update(InvActivityMapper::fromDbExtractFilledFields($dbDto));

        if (!$result->isSuccess()) {
            return InvActivityResult::failure(
                $result->getError() ?? new InvActivityException("Ошибка сохранения сделки")
            );
        }

        return InvActivityResult::success($invActivity);
    }


    /**
     * Создание новой инвестиционной сделки.
     *
     * @param  InvActivityInputDto $input
     * @return IInvActivityResult
     */
    public function create(InvActivityInputDto $input): IInvActivityResult
    {
        try {
            $validation = $this->validator->validate($input);
            if (!$validation->isValid()) {
                return InvActivityResult::failure(
                    new InvActivityException('Ошибка валидации: ' . implode('; ', $validation->getErrors()))
                );
            }

            $input->InvActivityHash = InvActivityMapper::generateActivityHash($input);

            // Если сделка закрытая — проставим close_time и вычислим result
            if (DealType::equals($input->type, DealType::CLOSED)) {
                $input->closeTime ??= (new DateTimeImmutable())->format('Y-m-d H:i:s');

                if ($input->closePrice === null) {
                    return InvActivityResult::failure(new InvActivityException("Цена закрытия обязательна для закрытой сделки"));
                }

                // Вычислить результат
                $input->result = self::calculateResult($input);
            }

            $dto = InvActivityMapper::fromInputToDb($input);
            $isHashExists = $this->repository->getAllByColumnValues('activity_hash', [$input->InvActivityHash]);
            if ($isHashExists->count() > 0) {
                return InvActivityResult::failure(new InvActivityException('Такая сделка уже существует.'));
            }

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


    /**
     * Обновление существующей сделки.
     *
     * @param  InvActivityInputDto $input
     * @return IInvActivityResult
     */
    public function updateById(InvActivityInputDto $input): IInvActivityResult
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

    /**
     * Удаление сделки по ID.
     *
     * @param  int $id
     * @return IInvActivityResult
     */
    public function deleteById(int $id): IInvActivityResult
    {
        try {
            if ($id <= 0) {
                return InvActivityResult::failure(new InvActivityException("Некорректный ID для удаления"));
            }

            $result = $this->repository->deleteById($id);

            if (!$result->isSuccess()) {
                return InvActivityResult::failure(
                    $result->getError() ?? new InvActivityException("Ошибка при удалении сделки")
                );
            }

            return InvActivityResult::success($id);
        } catch (\Throwable $e) {
            return InvActivityResult::failure($e);
        }
    }
}
