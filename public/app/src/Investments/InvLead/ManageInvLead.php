<?php

namespace crm\src\Investments\InvLead;

use crm\src\_common\interfaces\IValidation;
use crm\src\Investments\InvLead\_entities\InvLead;
use crm\src\Investments\InvStatus\_entities\InvStatus;
use crm\src\Investments\InvLead\_entities\SimpleInvLead;
use crm\src\Investments\InvLead\_common\DTOs\DbInvLeadDto;
use crm\src\Investments\InvLead\_common\DTOs\InvLeadInputDto;
use crm\src\Investments\InvLead\_exceptions\InvLeadException;
use crm\src\Investments\InvLead\_common\mappers\InvLeadMapper;
use crm\src\Investments\InvLead\_common\adapters\InvLeadResult;
use crm\src\Investments\InvLead\_common\DTOs\InvAccountManagerDto;
use crm\src\Investments\InvLead\_common\interfaces\IInvLeadResult;
use crm\src\Investments\InvSource\_common\mappers\InvSourceMapper;
use crm\src\Investments\InvStatus\_common\mappers\InvStatusMapper;
use crm\src\Investments\InvLead\_common\interfaces\IInvLeadRepository;
use crm\src\Investments\InvSource\_common\interfaces\IInvSourceResult;
use crm\src\Investments\InvStatus\_common\interfaces\IInvStatusResult;
use crm\src\Investments\InvSource\_common\interfaces\IInvSourceRepository;
use crm\src\Investments\InvStatus\_common\interfaces\IInvStatusRepository;
use crm\src\Investments\InvLead\_common\interfaces\IInvAccountManagerRepository;

/**
 * Сервис управления инвестиционными лидами.
 */
class ManageInvLead
{
    public function __construct(
        private IInvSourceRepository $invSourceRepo,
        private IInvStatusRepository $invStatusRepo,
        private IInvLeadRepository $repository,
        private IInvAccountManagerRepository $accountManagerRepo,
        private IValidation $validator,
    ) {
    }

    public function hydrateLead(SimpleInvLead $lead): SimpleInvLead
    {
        $source = $this->invSourceRepo->getById($lead->source?->id ?? 0);
        $status = $this->invStatusRepo->getById($lead->status?->id ?? 0);
        $accountManager = $this->accountManagerRepo->getById($lead->accountManager?->id ?? 0);

        $lead->source = null;
        $lead->status = null;
        $lead->accountManager = null;

        if ($source instanceof IInvSourceResult && $source->isSuccess()) {
            $lead->source = InvSourceMapper::fromDbToEntity($source->getData()); //$source->getData();
        }

        if ($status instanceof IInvStatusResult && $status->isSuccess()) {
            $lead->status = InvStatusMapper::fromDbToEntity($status->getData()); //$status->getInvStatus();
        }

        if ($accountManager !== null && isset($accountManager->login) && isset($accountManager->id)) {
            $lead->accountManager = new InvAccountManagerDto(
                id: $accountManager->id,
                login: $accountManager->login
            ); //$accountManager->getInvStatus();
        }

        return $lead;
    }

    public function buildLeadResponse(DbInvLeadDto|int|string $lead): InvLeadResult
    {
        $leadUid = $lead instanceof DbInvLeadDto ? $lead->uid : $lead;
        $dtoRes = $this->repository->getByUid((string)$leadUid);
        if (!$dtoRes->isSuccess()) {
            return InvLeadResult::failure(
                $dtoRes->getError() ?? new InvLeadException("Сохранение лида прошло успешно, но Ошибка при получении лида.")
            );
        }

        if ($dtoRes->getDtoLead() !== null) {
            return InvLeadResult::success($this->hydrateLead(
                InvLeadMapper::fromDbToEntity($dtoRes->getDtoLead())
            ));
        }

        if ($dtoRes->getInvLead() !== null) {
            return InvLeadResult::success($this->hydrateLead(
                $dtoRes->getInvLead()
            ));
        }

        return InvLeadResult::failure(new InvLeadException("Сохранение лида прошло успешно, но Ошибка при получении лида."));
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

        if ($randomLength < 1) {
            throw new \InvalidArgumentException('Длина UID должна быть больше длины префикса');
        }

        for ($i = 0; $i < $maxAttempts; $i++) {
            $min = 10 ** ($randomLength - 1);
            $max = (10 ** $randomLength) - 1;

            if ($min >= $max) {
                throw new \LogicException('Минимальное значение должно быть меньше максимального при генерации UID');
            }

            /**
             * @phpstan-ignore-next-line
             */
            $randomPart = random_int($min, $max);
            $uid = $prefix . $randomPart;

            // Проверяем, что UID уникален, если не найден, то возвращаем
            if (!$this->repository->getByUid($uid)->isSuccess()) {
                return $uid;
            }
        }

        throw new \RuntimeException("Не удалось сгенерировать уникальный UID после {$maxAttempts} попыток");
    }


    /**
     * Создание инвестиционного лида с возращением существующего лида.
     */
    public function createLeadWithReturn(InvLeadInputDto $input): IInvLeadResult
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

            $dto = InvLeadMapper::fromInputToDb($input);
            $result = $this->repository->save($dto);

            if (!$result->isSuccess()) {
                return InvLeadResult::failure(
                    $result->getError() ?? new InvLeadException("Ошибка при сохранении лида")
                );
            }

            return $this->buildLeadResponse((string)$input->uid);
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

            return $this->buildLeadResponse((string)$input->uid);
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
