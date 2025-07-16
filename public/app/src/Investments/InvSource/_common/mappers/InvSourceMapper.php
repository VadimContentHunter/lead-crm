<?php

namespace crm\src\Investments\InvSource\_common\mappers;

use crm\src\Investments\InvSource\_entities\InvSource;
use crm\src\Investments\InvSource\_common\DTOs\DbInvSourceDto;
use crm\src\Investments\InvSource\_common\DTOs\InvSourceInputDto;

/**
 * Маппер для преобразования между сущностями источника и DTO.
 */
class InvSourceMapper
{
    /**
     * Преобразует DTO из БД в сущность.
     *
     * @param  DbInvSourceDto $dto
     * @return InvSource
     */
    public static function fromDbToEntity(DbInvSourceDto $dto): InvSource
    {
        return new InvSource(
            code: $dto->code,
            label: $dto->label
        );
    }

    /**
     * Преобразует сущность в DTO для БД.
     *
     * @param  InvSource $entity
     * @param  int|null $id
     * @return DbInvSourceDto
     */
    public static function fromEntityToDb(InvSource $entity, ?int $id = null): DbInvSourceDto
    {
        return new DbInvSourceDto(
            code: $entity->code,
            label: $entity->label,
            id: $id,
        );
    }

    /**
     * Преобразует входной DTO в сущность.
     *
     * @param  InvSourceInputDto $dto
     * @return InvSource
     *
     * @throws \InvalidArgumentException
     */
    public static function fromInputToEntity(InvSourceInputDto $dto): InvSource
    {
        return new InvSource(
            code: $dto->code ?? throw new \InvalidArgumentException('code is required'),
            label: $dto->label ?? throw new \InvalidArgumentException('label is required'),
        );
    }

    /**
     * Преобразует входной DTO напрямую в DTO для БД.
     *
     * @param  InvSourceInputDto $dto
     * @return DbInvSourceDto
     *
     * @throws \InvalidArgumentException
     */
    public static function fromInputToDb(InvSourceInputDto $dto): DbInvSourceDto
    {
        return new DbInvSourceDto(
            code: $dto->code ?? throw new \InvalidArgumentException('code is required'),
            label: $dto->label ?? throw new \InvalidArgumentException('label is required'),
            id: null
        );
    }

    /**
     * Преобразует DTO для БД в ассоциативный массив.
     *
     * @param  DbInvSourceDto $dto
     * @return array<string,mixed>
     */
    public static function fromDbToArray(DbInvSourceDto $dto): array
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
     * @return DbInvSourceDto
     */
    public static function fromArrayToDb(array $data): DbInvSourceDto
    {
        return new DbInvSourceDto(
            code: $data['code'],
            label: $data['label'],
            id: isset($data['id']) ? (int) $data['id'] : null,
        );
    }

    /**
     * Преобразует массив в входной DTO (InvSourceInputDto).
     *
     * @param  array<string, mixed> $data
     * @return InvSourceInputDto
     */
    public static function fromArrayToInput(array $data): InvSourceInputDto
    {
        return new InvSourceInputDto(
            code: isset($data['code']) ? (string) $data['code'] : null,
            label: isset($data['label']) ? (string) $data['label'] : null,
            id: isset($data['id']) ? (int) $data['id'] : null,
        );
    }


    /**
     * Возвращает только заполненные поля из входного DTO.
     *
     * @param  InvSourceInputDto $dto
     * @return array<string,mixed>
     */
    public static function fromInputExtractFilledFields(InvSourceInputDto $dto): array
    {
        $fields = [];

        if ($dto->id !== null) {
            $fields['id'] = $dto->id;
        }
        if ($dto->code !== null) {
            $fields['code'] = $dto->code;
        }
        if ($dto->label !== null) {
            $fields['label'] = $dto->label;
        }

        return $fields;
    }
}
