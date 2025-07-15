<?php

namespace crm\src\Investments\Status\_mappers;

use Domain\Investment\InvStatus;
use Domain\Investment\DTOs\DbInvStatusDto;
use Domain\Investment\DTOs\InvStatusInputDto;

/**
 * Маппер для преобразования между сущностями статуса и DTO.
 */
class StatusMapper
{
    /**
     * Преобразует DTO из БД в сущность.
     *
     * @param  DbInvStatusDto $dto
     * @return InvStatus
     */
    public static function fromDbToEntity(DbInvStatusDto $dto): InvStatus
    {
        return new InvStatus(
            id: $dto->id ?? throw new \InvalidArgumentException('id is required'),
            code: $dto->code,
            label: $dto->label
        );
    }

    /**
     * Преобразует сущность в DTO для БД.
     *
     * @param  InvStatus $entity
     * @param  int|null $id
     * @return DbInvStatusDto
     */
    public static function fromEntityToDb(InvStatus $entity, ?int $id = null): DbInvStatusDto
    {
        return new DbInvStatusDto(
            id: $id ?? $entity->id,
            code: $entity->code,
            label: $entity->label,
        );
    }

    /**
     * Преобразует входной DTO в сущность.
     *
     * @param  InvStatusInputDto $dto
     * @return InvStatus
     */
    public static function fromInputToEntity(InvStatusInputDto $dto): InvStatus
    {
        return new InvStatus(
            id: 0, // временный placeholder, предполагается, что id задаётся позже
            code: $dto->code ?? throw new \InvalidArgumentException('code is required'),
            label: $dto->label ?? throw new \InvalidArgumentException('label is required'),
        );
    }

    /**
     * Преобразует входной DTO напрямую в DTO для БД.
     *
     * @param  InvStatusInputDto $dto
     * @return DbInvStatusDto
     */
    public static function fromInputToDb(InvStatusInputDto $dto): DbInvStatusDto
    {
        return new DbInvStatusDto(
            id: null,
            code: $dto->code ?? throw new \InvalidArgumentException('code is required'),
            label: $dto->label ?? throw new \InvalidArgumentException('label is required'),
        );
    }

    /**
     * Преобразует DTO для БД в массив.
     *
     * @param  DbInvStatusDto $dto
     * @return array<string,mixed>
     */
    public static function fromDbToArray(DbInvStatusDto $dto): array
    {
        return [
            'id' => $dto->id,
            'code' => $dto->code,
            'label' => $dto->label,
        ];
    }

    /**
     * Преобразует массив данных из БД в DTO.
     *
     * @param  array<string, mixed> $data
     * @return DbInvStatusDto
     */
    public static function fromArrayToDb(array $data): DbInvStatusDto
    {
        return new DbInvStatusDto(
            id: isset($data['id']) ? (int) $data['id'] : null,
            code: $data['code'],
            label: $data['label'],
        );
    }

    /**
     * Возвращает только заполненные поля из входного DTO.
     *
     * @param  InvStatusInputDto $dto
     * @return array<string,mixed>
     */
    public static function fromInputExtractFilledFields(InvStatusInputDto $dto): array
    {
        $fields = [];

        if ($dto->code !== null) {
            $fields['code'] = $dto->code;
        }

        if ($dto->label !== null) {
            $fields['label'] = $dto->label;
        }

        return $fields;
    }
}
