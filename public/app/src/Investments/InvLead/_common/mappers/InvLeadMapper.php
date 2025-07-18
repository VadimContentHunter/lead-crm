<?php

namespace crm\src\Investments\InvLead\_common\mappers;

use DateTimeImmutable;
use crm\src\Investments\InvSource\_entities\InvSource;
use crm\src\Investments\InvStatus\_entities\InvStatus;
use crm\src\Investments\InvLead\_entities\SimpleInvLead;
use crm\src\Investments\InvLead\_common\DTOs\DbInvLeadDto;
use crm\src\Investments\InvLead\_common\DTOs\InvLeadInputDto;
use crm\src\Investments\InvLead\_common\DTOs\InvAccountManagerDto;

/**
 * Маппер для преобразования между DTO и сущностью инвестиционного лида.
 */
class InvLeadMapper
{
    /**
     * Преобразует DTO из базы данных в сущность SimpleInvLead.
     *
     * @param  DbInvLeadDto $dto
     * @return SimpleInvLead
     */
    public static function fromDbToEntity(DbInvLeadDto $dto): SimpleInvLead
    {
        return new SimpleInvLead(
            uid: $dto->uid,
            contact: $dto->contact,
            phone: $dto->phone,
            email: $dto->email,
            fullName: $dto->fullName,
            createdAt: new DateTimeImmutable($dto->createdAt),
            accountManager: $dto->accountManagerId !== null
                ? new InvAccountManagerDto($dto->accountManagerId, '') // login неизвестен
                : null,
            visible: $dto->visible,
            source: $dto->sourceId !== null ? new InvSource($dto->sourceId, '', '') : null,
            status: $dto->statusId !== null ? new InvStatus($dto->statusId, '', '') : null
        );
    }

    /**
     * Преобразует сущность SimpleInvLead в DTO для БД.
     *
     * @param  SimpleInvLead $entity
     * @return DbInvLeadDto
     */
    public static function fromEntityToDb(SimpleInvLead $entity): DbInvLeadDto
    {
        return new DbInvLeadDto(
            uid: $entity->uid,
            createdAt: $entity->createdAt?->format('Y-m-d H:i:s') ?? (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            contact: $entity->contact,
            phone: $entity->phone,
            email: $entity->email,
            fullName: $entity->fullName,
            accountManagerId: $entity->accountManager?->id,
            visible: $entity->visible,
            sourceId: $entity->source?->id ?? null,
            statusId: $entity->status?->id ?? null,
        );
    }

    /**
     * Преобразует входной DTO в сущность SimpleInvLead.
     *
     * @param  InvLeadInputDto $dto
     * @param  string $uid
     * @return SimpleInvLead
     */
    public static function fromInputToEntity(InvLeadInputDto $dto, string $uid): SimpleInvLead
    {
        $manager = $dto->accountManagerId !== null
            ? new InvAccountManagerDto($dto->accountManagerId, '') // логин неизвестен
            : null;

        return new SimpleInvLead(
            uid: $uid,
            contact: $dto->contact ?? '',
            phone: $dto->phone ?? '',
            email: $dto->email ?? '',
            fullName: $dto->fullName ?? '',
            createdAt: new DateTimeImmutable(),
            accountManager: $manager,
            visible: $dto->visible ?? true,
            source: null,
            status: null
        );
    }

    /**
     * Преобразует входной DTO в DTO для базы данных.
     *
     * @param  InvLeadInputDto $dto
     * @param  string $uid
     * @return DbInvLeadDto
     */
    public static function fromInputToDb(InvLeadInputDto $dto, ?string $createdAt = null): DbInvLeadDto
    {
        return new DbInvLeadDto(
            uid: $dto->uid ?? throw new \InvalidArgumentException('uid is required'),
            createdAt: $createdAt ?? (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            contact: $dto->contact ?? '',
            phone: $dto->phone ?? '',
            email: $dto->email ?? '',
            fullName: $dto->fullName ?? '',
            accountManagerId: $dto->accountManagerId,
            visible: $dto->visible ?? true,
            sourceId: $dto->sourceId,
            statusId: $dto->statusId,
        );
    }

    /**
     * Преобразует DTO из БД в ассоциативный массив.
     *
     * @param  DbInvLeadDto $dto
     * @return array<string, mixed>
     */
    public static function fromDbToArray(DbInvLeadDto $dto): array
    {
        return [
            'uid' => $dto->uid,
            'created_at' => $dto->createdAt,
            'contact' => $dto->contact,
            'phone' => $dto->phone,
            'email' => $dto->email,
            'full_name' => $dto->fullName,
            'account_manager_id' => $dto->accountManagerId,
            'visible' => $dto->visible,
            'source_id' => $dto->sourceId,
            'status_id' => $dto->statusId,
        ];
    }

    /**
     * Преобразует массив в DTO для базы данных.
     *
     * @param  array<string, mixed> $data
     * @return DbInvLeadDto
     */
    public static function fromArrayToDb(array $data): DbInvLeadDto
    {
        return new DbInvLeadDto(
            uid: InvLeadArrayNormalizer::normalizeByFieldName('uid', $data) ?? throw new \InvalidArgumentException('uid is required'),
            createdAt: (string) ($data['created_at'] ?? (new DateTimeImmutable())->format('Y-m-d H:i:s')),
            contact: InvLeadArrayNormalizer::normalizeByFieldName('contact', $data) ?? '',
            phone: InvLeadArrayNormalizer::normalizeByFieldName('phone', $data) ?? '',
            email: InvLeadArrayNormalizer::normalizeByFieldName('email', $data) ?? '',
            fullName: InvLeadArrayNormalizer::normalizeByFieldName('full_name', $data) ?? '',
            accountManagerId: InvLeadArrayNormalizer::normalizeByFieldName('account_manager_id', $data),
            visible: InvLeadArrayNormalizer::normalizeByFieldName('visible', $data) ?? true,
            sourceId: InvLeadArrayNormalizer::normalizeByFieldName('source_id', $data),
            statusId: InvLeadArrayNormalizer::normalizeByFieldName('status_id', $data),
        );
    }

    /**
     * Преобразует массив в входной DTO (InvLeadInputDto).
     *
     * @param  array<string, mixed> $data
     * @return InvLeadInputDto
     */
    public static function fromArrayToInput(array $data): InvLeadInputDto
    {
        return new InvLeadInputDto(
            uid: InvLeadArrayNormalizer::normalizeByFieldName('uid', $data),
            contact: InvLeadArrayNormalizer::normalizeByFieldName('contact', $data),
            phone: InvLeadArrayNormalizer::normalizeByFieldName('phone', $data),
            email: InvLeadArrayNormalizer::normalizeByFieldName('email', $data),
            fullName: InvLeadArrayNormalizer::normalizeByFieldName('full_name', $data),
            accountManagerId: InvLeadArrayNormalizer::normalizeByFieldName('account_manager_id', $data),
            visible: InvLeadArrayNormalizer::normalizeByFieldName('visible', $data),
            sourceId: InvLeadArrayNormalizer::normalizeByFieldName('source_id', $data),
            statusId: InvLeadArrayNormalizer::normalizeByFieldName('status_id', $data),
        );
    }


    /**
     * Извлекает только заполненные поля из входного DTO.
     *
     * @param  InvLeadInputDto $dto
     * @return array<string, mixed>
     */
    public static function fromInputExtractFilledFields(InvLeadInputDto $dto): array
    {
        $fields = [];

        if ($dto->uid !== null) {
            $fields['uid'] = $dto->uid;
        }

        if ($dto->contact !== null) {
            $fields['contact'] = $dto->contact;
        }

        if ($dto->phone !== null) {
            $fields['phone'] = $dto->phone;
        }

        if ($dto->email !== null) {
            $fields['email'] = $dto->email;
        }

        if ($dto->fullName !== null) {
            $fields['full_name'] = $dto->fullName;
        }

        if ($dto->accountManagerId !== null) {
            $fields['account_manager_id'] = $dto->accountManagerId;
        }

        if ($dto->visible !== null) {
            $fields['visible'] = $dto->visible;
        }

        if ($dto->sourceId !== null) {
            $fields['source_id'] = $dto->sourceId;
        }

        if ($dto->statusId !== null) {
            $fields['status_id'] = $dto->statusId;
        }

        return $fields;
    }
}
