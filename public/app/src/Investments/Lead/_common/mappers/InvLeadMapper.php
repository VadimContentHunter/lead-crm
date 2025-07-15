<?php

namespace crm\src\Investments\Lead\_mappers;

use DateTimeImmutable;
use crm\src\Investments\Lead\_dto\DbInvLeadDto;
use crm\src\Investments\Lead\_dto\InvLeadInputDto;
use crm\src\Investments\Lead\_entities\SimpleInvLead;

/**
 * Маппер для преобразования между сущностью инвестиционного лида и DTO.
 */
class InvLeadMapper
{
    /**
     * Преобразует DTO из БД в сущность.
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
            accountManager: $dto->accountManager ?? '',
            visible: $dto->visible,
            source: null, // требуется подгрузка отдельно
            status: null  // требуется подгрузка отдельно
        );
    }

    /**
     * Преобразует сущность в DTO для БД.
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
            accountManager: $entity->accountManager,
            visible: $entity->visible,
            sourceId: $entity->source?->id ?? null,
            statusId: $entity->status?->id ?? null,
            balanceId: null
        );
    }

    /**
     * Преобразует входной DTO в сущность.
     *
     * @param  InvLeadInputDto $dto
     * @param  string $uid
     * @return SimpleInvLead
     */
    public static function fromInputToEntity(InvLeadInputDto $dto, string $uid): SimpleInvLead
    {
        return new SimpleInvLead(
            uid: $uid,
            contact: $dto->contact ?? '',
            phone: $dto->phone ?? '',
            email: $dto->email ?? '',
            fullName: $dto->fullName ?? '',
            createdAt: new DateTimeImmutable(),
            accountManager: $dto->accountManager ?? '',
            visible: $dto->visible ?? true,
            source: null,
            status: null
        );
    }

    /**
     * Преобразует входной DTO напрямую в DTO для БД.
     *
     * @param  InvLeadInputDto $dto
     * @param  string $uid
     * @param  string|null $createdAt
     * @return DbInvLeadDto
     */
    public static function fromInputToDb(InvLeadInputDto $dto, string $uid, ?string $createdAt = null): DbInvLeadDto
    {
        return new DbInvLeadDto(
            uid: $uid,
            createdAt: $createdAt ?? (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            contact: $dto->contact ?? '',
            phone: $dto->phone ?? '',
            email: $dto->email ?? '',
            fullName: $dto->fullName ?? '',
            accountManager: $dto->accountManager,
            visible: $dto->visible ?? true,
            sourceId: $dto->sourceId,
            statusId: $dto->statusId,
            balanceId: null
        );
    }

    /**
     * Преобразует DTO из БД в массив.
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
            'account_manager' => $dto->accountManager,
            'visible' => $dto->visible,
            'source_id' => $dto->sourceId,
            'status_id' => $dto->statusId,
            'balance_id' => $dto->balanceId,
        ];
    }

    /**
     * Преобразует массив из БД в DTO.
     *
     * @param  array<string, mixed> $data
     * @return DbInvLeadDto
     */
    public static function fromArrayToDb(array $data): DbInvLeadDto
    {
        return new DbInvLeadDto(
            uid: (string) $data['uid'],
            createdAt: (string) $data['created_at'],
            contact: (string) ($data['contact'] ?? ''),
            phone: (string) ($data['phone'] ?? ''),
            email: (string) ($data['email'] ?? ''),
            fullName: (string) ($data['full_name'] ?? ''),
            accountManager: $data['account_manager'] ?? null,
            visible: (bool) ($data['visible'] ?? true),
            sourceId: isset($data['source_id']) ? (int) $data['source_id'] : null,
            statusId: isset($data['status_id']) ? (int) $data['status_id'] : null,
            balanceId: isset($data['balance_id']) ? (int) $data['balance_id'] : null
        );
    }

    /**
     * Возвращает только заполненные поля из входного DTO.
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

        if ($dto->accountManager !== null) {
            $fields['account_manager'] = $dto->accountManager;
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
