<?php

namespace crm\src\Investments\Activity\_mappers;

use DateTimeImmutable;
use crm\src\Investments\Activity\_entities\DealType;
use crm\src\Investments\Activity\_entities\InvActivity;
use crm\src\Investments\Activity\_entities\DealDirection;
use crm\src\Investments\Activity\_common\DTOs\DbActivityDto;
use crm\src\Investments\Activity\_common\DTOs\ActivityInputDto;

/**
 * Маппер между сущностью InvActivity и различными DTO.
 */
class ActivityMapper
{
    /**
     * Преобразует DTO из БД в сущность.
     *
     * @param  DbActivityDto $dto
     * @return InvActivity
     */
    public static function fromDbToEntity(DbActivityDto $dto): InvActivity
    {
        return new InvActivity(
            activityHash: $dto->activity_hash,
            leadUid: $dto->lead_uid,
            type: DealType::from($dto->type),
            openTime: new DateTimeImmutable($dto->open_time),
            closeTime: $dto->close_time ? new DateTimeImmutable($dto->close_time) : null,
            pair: $dto->pair,
            openPrice: $dto->open_price,
            closePrice: $dto->close_price,
            amount: $dto->amount,
            direction: DealDirection::from($dto->direction),
            result: $dto->result,
            id: $dto->id,
        );
    }

    /**
     * Преобразует сущность в DTO для БД.
     *
     * @param  InvActivity $entity
     * @return DbActivityDto
     */
    public static function fromEntityToDb(InvActivity $entity): DbActivityDto
    {
        return new DbActivityDto(
            id: $entity->id,
            activity_hash: $entity->activityHash,
            lead_uid: $entity->leadUid,
            type: $entity->type->value,
            open_time: $entity->openTime->format('Y-m-d H:i:s'),
            close_time: $entity->closeTime?->format('Y-m-d H:i:s'),
            pair: $entity->pair,
            open_price: $entity->openPrice,
            close_price: $entity->closePrice,
            amount: $entity->amount,
            direction: $entity->direction->value,
            result: $entity->result,
        );
    }

    /**
     * Преобразует входной DTO в доменную сущность InvActivity.
     *
     * @param  ActivityInputDto $dto
     * @return InvActivity
     */
    public static function fromInputToEntity(ActivityInputDto $dto): InvActivity
    {
        return new InvActivity(
            activityHash: $dto->activityHash ?? uniqid('act_', true),
            leadUid: $dto->leadUid ?? throw new \InvalidArgumentException('leadUid is required'),
            type: $dto->type ? DealType::from($dto->type) : DealType::ACTIVE,
            openTime: $dto->openTime ? new DateTimeImmutable($dto->openTime) : new DateTimeImmutable(),
            closeTime: $dto->closeTime ? new DateTimeImmutable($dto->closeTime) : null,
            pair: $dto->pair ?? '',
            openPrice: $dto->openPrice ?? 0.0,
            closePrice: $dto->closePrice,
            amount: $dto->amount ?? 0.0,
            direction: $dto->direction ? DealDirection::from($dto->direction) : DealDirection::LONG,
            result: $dto->result,
            id: $dto->id,
        );
    }

    /**
     * Преобразует входной DTO напрямую в DTO для БД.
     *
     * @param  ActivityInputDto $dto
     * @return DbActivityDto
     */
    public static function fromInputToDb(ActivityInputDto $dto): DbActivityDto
    {
        $activityHash = $dto->activityHash ?? uniqid('act_', true);
        $leadUid = $dto->leadUid ?? throw new \InvalidArgumentException('leadUid is required');
        $type = $dto->type ?? DealType::ACTIVE->value;
        $direction = $dto->direction ?? DealDirection::LONG->value;

        $openTime = $dto->openTime
            ? (new DateTimeImmutable($dto->openTime))->format('Y-m-d H:i:s')
            : (new DateTimeImmutable())->format('Y-m-d H:i:s');

        $closeTime = $dto->closeTime
            ? (new DateTimeImmutable($dto->closeTime))->format('Y-m-d H:i:s')
            : null;

        return new DbActivityDto(
            id: $dto->id,
            activity_hash: $activityHash,
            lead_uid: $leadUid,
            type: $type,
            open_time: $openTime,
            close_time: $closeTime,
            pair: $dto->pair ?? '',
            open_price: $dto->openPrice ?? 0.0,
            close_price: $dto->closePrice,
            amount: $dto->amount ?? 0.0,
            direction: $direction,
            result: $dto->result,
        );
    }
}
