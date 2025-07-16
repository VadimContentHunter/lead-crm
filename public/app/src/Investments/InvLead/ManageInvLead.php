<?php

namespace crm\src\Investments\InvLead;

use crm\src\_common\interfaces\IValidation;
use crm\src\Investments\InvLead\_common\DTOs\InvLeadInputDto;
use crm\src\Investments\InvLead\_exceptions\InvLeadException;
use crm\src\Investments\InvLead\_common\mappers\InvLeadMapper;
use crm\src\Investments\InvLead\_common\adapters\InvLeadResult;
use crm\src\Investments\InvLead\_common\interfaces\IInvLeadResult;
use crm\src\Investments\InvLead\_common\interfaces\IInvLeadRepository;

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
     * Генерирует уникальный UID для инвестиционного лида.
     *
     * @param  string $prefix      Первые 3 цифры UID (например, "928")
     * @param  int $length      Общая длина UID (по умолчанию 9)
     * @param  int $maxAttempts Максимум попыток генерации (по умолчанию 3)
     * @return string
     *
     * @throws \RuntimeException Если не удалось сгенерировать уникальный UID
     */
    private function generateUniqueUid(string $prefix = '928', int $length = 9, int $maxAttempts = 3): string
    {
        $prefixLength = strlen($prefix);
        $randomLength = $length - $prefixLength;

        if ($randomLength <= 0) {
            throw new \InvalidArgumentException('Длина UID должна быть больше длины префикса');
        }

        for ($i = 0; $i < $maxAttempts; $i++) {
            $min = 10 ** ($randomLength - 1);
            $max = (10 ** $randomLength) - 1;
            $randomPart = random_int($min, $max);
            $uid = $prefix . $randomPart;

            if ($this->repository->getByUid($uid)->isSuccess()) {
                return $uid;
            }
        }

        throw new \RuntimeException("Не удалось сгенерировать уникальный UID после {$maxAttempts} попыток");
    }


    /**
     * Создание нового лида.
     */
    public function create(InvLeadInputDto $input): IInvLeadResult
    {
        try {
            $input->uid = $this->generateUniqueUid();

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
