<?php

namespace crm\src\Investments\InvStatus\_common\mappers;

use crm\src\Investments\InvStatus\_entities\InvStatus;
use crm\src\Investments\InvStatus\_common\DTOs\DbInvStatusDto;
use crm\src\Investments\InvStatus\_common\DTOs\InvStatusInputDto;

/**
 * Маппер для преобразования между сущностями статуса и DTO.
 */
class InvStatusMapper
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
     * Преобразует массив в входной DTO (InvStatusInputDto).
     *
     * @param  array<string, mixed> $data
     * @return InvStatusInputDto
     */
    public static function fromArrayToInput(array $data): InvStatusInputDto
    {
        return new InvStatusInputDto(
            code: isset($data['code']) ? (string) $data['code'] : null,
            label: isset($data['label']) ? (string) $data['label'] : null,
            id: isset($data['id']) ? (int) $data['id'] : null
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
