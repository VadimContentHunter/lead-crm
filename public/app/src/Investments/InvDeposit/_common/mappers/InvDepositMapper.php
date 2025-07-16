<?php

namespace crm\src\Investments\InvDeposit\_mappers;

use DateTimeImmutable;
use crm\src\Investments\InvDeposit\_entities\InvDeposit;
use crm\src\Investments\InvDeposit\_common\DTOs\DbInvDepositDto;
use crm\src\Investments\InvDeposit\_common\DTOs\InvDepositInputDto;

class InvDepositMapper
{
    /**
     * Преобразует DTO из БД в сущность.
     *
     * @param  DbInvDepositDto $dto
     * @return InvDeposit
     */
    public static function fromDbToEntity(DbInvDepositDto $dto): InvDeposit
    {
        return new InvDeposit(
            id: $dto->id,
            uid: $dto->uid,
            sum: $dto->sum,
            createdAt: new DateTimeImmutable($dto->created),
        );
    }

    /**
     * Преобразует сущность в DTO для БД.
     *
     * @param  InvDeposit $entity
     * @return DbInvDepositDto
     */
    public static function fromEntityToDb(InvDeposit $entity): DbInvDepositDto
    {
        return new DbInvDepositDto(
            id: $entity->id,
            uid: $entity->uid,
            sum: $entity->sum,
            created: $entity->createdAt->format('Y-m-d H:i:s'),
        );
    }

    /**
     * Преобразует входной DTO в сущность.
     *
     * @param  InvDepositInputDto $dto
     * @return InvDeposit
     */
    public static function fromInputToEntity(InvDepositInputDto $dto): InvDeposit
    {
        return new InvDeposit(
            id: $dto->id ?? 0,
            uid: $dto->uid ?? throw new \InvalidArgumentException('uid is required'),
            sum: $dto->sum ?? 0.0,
            createdAt: $dto->created ? new DateTimeImmutable($dto->created) : null,
        );
    }

    /**
     * Преобразует входной DTO напрямую в DTO для БД.
     *
     * @param  InvDepositInputDto $dto
     * @return DbInvDepositDto
     */
    public static function fromInputToDb(InvDepositInputDto $dto): DbInvDepositDto
    {
        return new DbInvDepositDto(
            id: $dto->id ?? 0,
            uid: $dto->uid ?? throw new \InvalidArgumentException('uid is required'),
            sum: $dto->sum ?? 0.0,
            created: $dto->created ?? (new DateTimeImmutable())->format('Y-m-d H:i:s'),
        );
    }

    /**
     * Преобразует DTO для БД в ассоциативный массив.
     *
     * @param  DbInvDepositDto $dto
     * @return array<string, mixed>
     */
    public static function fromDbToArray(DbInvDepositDto $dto): array
    {
        return [
            'id' => $dto->id,
            'uid' => $dto->uid,
            'sum' => $dto->sum,
            'created' => $dto->created,
        ];
    }

    /**
     * Преобразует массив данных в DTO для БД.
     *
     * @param  array<string, mixed> $data
     * @return DbInvDepositDto
     */
    public static function fromArrayToDb(array $data): DbInvDepositDto
    {
        return new DbInvDepositDto(
            id: (int) $data['id'],
            uid: (string) $data['uid'],
            sum: (float) $data['sum'],
            created: (string) $data['created'],
        );
    }

    /**
     * Извлекает только непустые поля из входного DTO.
     *
     * @param  InvDepositInputDto $dto
     * @return array<string, mixed>
     */
    public static function fromInputExtractFilledFields(InvDepositInputDto $dto): array
    {
        $fields = [];

        if ($dto->id !== null) {
            $fields['id'] = $dto->id;
        }

        if ($dto->uid !== null) {
            $fields['uid'] = $dto->uid;
        }

        if ($dto->sum !== null) {
            $fields['sum'] = $dto->sum;
        }

        if ($dto->created !== null) {
            $fields['created'] = (new DateTimeImmutable($dto->created))->format('Y-m-d H:i:s');
        }

        return $fields;
    }
}
