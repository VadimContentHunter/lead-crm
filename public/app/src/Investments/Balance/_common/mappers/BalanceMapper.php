<?php

namespace crm\src\Investments\Balance\_mappers;

use crm\src\Investments\Balance\_entities\InvBalance;
use crm\src\Investments\Balance\_common\DTOs\DbInvBalanceDto;
use crm\src\Investments\Balance\_common\DTOs\InputInvBalanceDto;

class BalanceMapper
{
    /**
     * Преобразует DTO из БД в сущность.
     *
     * @param  DbInvBalanceDto $dto
     * @return InvBalance
     */
    public static function fromDbToEntity(DbInvBalanceDto $dto): InvBalance
    {
        return new InvBalance(
            leadUid: $dto->lead_uid,
            current: $dto->current,
            deposit: $dto->deposit,
            potation: $dto->potation,
            active: $dto->active,
        );
    }

    /**
     * Преобразует сущность в DTO для БД.
     *
     * @param  InvBalance $entity
     * @return DbInvBalanceDto
     */
    public static function fromEntityToDb(InvBalance $entity): DbInvBalanceDto
    {
        return new DbInvBalanceDto(
            lead_uid: $entity->leadUid,
            current: $entity->current,
            deposit: $entity->deposit,
            potation: $entity->potation,
            active: $entity->active,
        );
    }

    /**
     * Преобразует входной DTO в сущность.
     *
     * @param  InputInvBalanceDto $dto
     * @return InvBalance
     */
    public static function fromInputToEntity(InputInvBalanceDto $dto): InvBalance
    {
        return new InvBalance(
            leadUid: $dto->leadUid,
            deposit: $dto->deposit,
            potation: $dto->potation,
            current: 0.0,
            active: 0.0,
        );
    }

    /**
     * Преобразует входной DTO напрямую в DTO для БД.
     *
     * @param  InputInvBalanceDto $dto
     * @return DbInvBalanceDto
     */
    public static function fromInputToDb(InputInvBalanceDto $dto): DbInvBalanceDto
    {
        return new DbInvBalanceDto(
            lead_uid: $dto->leadUid,
            deposit: $dto->deposit,
            potation: $dto->potation,
            current: 0.0,
            active: 0.0,
        );
    }

    /**
     * Преобразует DTO для БД в ассоциативный массив для сохранения в базу данных.
     *
     * @param  DbInvBalanceDto $dto
     * @return array<string, float|string>
     */
    public static function fromDbToArray(DbInvBalanceDto $dto): array
    {
        return [
            'lead_uid' => $dto->lead_uid,
            'current' => $dto->current,
            'deposit' => $dto->deposit,
            'potation' => $dto->potation,
            'active' => $dto->active,
        ];
    }

    /**
     * Преобразует ассоциативный массив данных из БД в DTO.
     *
     * @param  array<string, mixed> $data
     * @return DbInvBalanceDto
     */
    public static function fromArrayToDb(array $data): DbInvBalanceDto
    {
        return new DbInvBalanceDto(
            lead_uid: $data['lead_uid'],
            current: (float) ($data['current'] ?? 0.0),
            deposit: (float) ($data['deposit'] ?? 0.0),
            potation: (float) ($data['potation'] ?? 0.0),
            active: (float) ($data['active'] ?? 0.0),
        );
    }

    /**
     * Извлекает только непустые (не null) поля из InputInvBalanceDto в виде массива.
     * Полезно для обновлений, где нужно сохранить только переданные значения.
     *
     * @param  InputInvBalanceDto $dto
     * @return array<string, float|string>
     */
    public static function fromInputExtractFilledFields(InputInvBalanceDto $dto): array
    {
        $fields = [];

        if (!empty($dto->leadUid)) {
            $fields['lead_uid'] = $dto->leadUid;
        }

        if ($dto->deposit !== null) {
            $fields['deposit'] = $dto->deposit;
        }

        if ($dto->potation !== null) {
            $fields['potation'] = $dto->potation;
        }

        return $fields;
    }
}
