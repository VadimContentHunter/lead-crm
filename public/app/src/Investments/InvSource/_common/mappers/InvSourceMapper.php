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
     * Преобразует DTO из БД в сущность источника.
     *
     * @param  DbInvSourceDto $dto
     * @return InvSource
     *
     * @throws \InvalidArgumentException Если отсутствует id
     */
    public static function fromDbToEntity(DbInvSourceDto $dto): InvSource
    {
        return new InvSource(
            id: $dto->id ?? throw new \InvalidArgumentException('id is required'),
            code: $dto->code,
            label: $dto->label
        );
    }

    /**
     * Преобразует сущность источника в DTO для БД.
     *
     * @param  InvSource $entity
     * @param  int|null $id     Явно переданный ID (если
     *                          нужно переопределить)
     * @return DbInvSourceDto
     */
    public static function fromEntityToDb(InvSource $entity, ?int $id = null): DbInvSourceDto
    {
        return new DbInvSourceDto(
            id: $id ?? $entity->id,
            code: $entity->code,
            label: $entity->label
        );
    }

    /**
     * Преобразует входной DTO в сущность источника.
     *
     * @param  InvSourceInputDto $dto
     * @return InvSource
     *
     * @throws \InvalidArgumentException Если отсутствует обязательное поле
     */
    public static function fromInputToEntity(InvSourceInputDto $dto): InvSource
    {
        return new InvSource(
            id: $dto->id ?? 0,
            code: $dto->code ?? throw new \InvalidArgumentException('code is required'),
            label: $dto->label ?? throw new \InvalidArgumentException('label is required')
        );
    }

    /**
     * Преобразует входной DTO напрямую в DTO для БД.
     *
     * @param  InvSourceInputDto $dto
     * @return DbInvSourceDto
     *
     * @throws \InvalidArgumentException Если отсутствует обязательное поле
     */
    public static function fromInputToDb(InvSourceInputDto $dto): DbInvSourceDto
    {
        return new DbInvSourceDto(
            id: $dto->id,
            code: $dto->code ?? throw new \InvalidArgumentException('code is required'),
            label: $dto->label ?? throw new \InvalidArgumentException('label is required')
        );
    }

    /**
     * Преобразует DTO из БД в ассоциативный массив.
     *
     * @param  DbInvSourceDto $dto
     * @return array<string, mixed>
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
     * Преобразует ассоциативный массив в DTO из БД.
     *
     * @param  array<string, mixed> $data
     * @return DbInvSourceDto
     */
    public static function fromArrayToDb(array $data): DbInvSourceDto
    {
        return new DbInvSourceDto(
            id: InvSourceArrayNormalizer::normalizeByFieldName('id', $data),
            code: InvSourceArrayNormalizer::normalizeByFieldName('code', $data)
                    ?? throw new \InvalidArgumentException('code is required'),
            label: InvSourceArrayNormalizer::normalizeByFieldName('label', $data)
                    ?? throw new \InvalidArgumentException('label is required')
        );
    }

    /**
     * Преобразует массив в входной DTO источника.
     *
     * @param  array<string, mixed> $data
     * @return InvSourceInputDto
     */
    public static function fromArrayToInput(array $data): InvSourceInputDto
    {
        return new InvSourceInputDto(
            id: InvSourceArrayNormalizer::normalizeByFieldName('id', $data),
            code: InvSourceArrayNormalizer::normalizeByFieldName('code', $data),
            label: InvSourceArrayNormalizer::normalizeByFieldName('label', $data),
        );
    }

    /**
     * Извлекает только непустые (установленные) поля из входного DTO.
     *
     * @param  InvSourceInputDto $dto
     * @return array<string, mixed>
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
