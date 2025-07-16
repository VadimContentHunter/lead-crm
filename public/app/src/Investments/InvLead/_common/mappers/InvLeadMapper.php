<?php

namespace crm\src\Investments\InvLead\_mappers;

use DateTimeImmutable;
use crm\src\Investments\InvLead\_common\DTOs\DbInvLeadDto;
use crm\src\Investments\InvLead\_common\DTOs\InvLeadInputDto;
use crm\src\Investments\InvLead\_entities\SimpleInvLead;
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
            accountManager: $dto->accountManagerId !== null && $dto->accountManagerLogin !== null
                ? new InvAccountManagerDto($dto->accountManagerId, $dto->accountManagerLogin)
                : null,
            visible: $dto->visible,
            source: null,
            status: null
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
            accountManagerLogin: $entity->accountManager?->login,
            visible: $entity->visible,
            sourceId: $entity->source?->id ?? null,
            statusId: $entity->status?->id ?? null,
            InvBalanceId: null
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
        $manager = $dto->accountManagerId !== null && $dto->accountManagerLogin !== null
            ? new InvAccountManagerDto($dto->accountManagerId, $dto->accountManagerLogin)
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
            accountManagerId: $dto->accountManagerId,
            accountManagerLogin: $dto->accountManagerLogin,
            visible: $dto->visible ?? true,
            sourceId: $dto->sourceId,
            statusId: $dto->statusId,
            InvBalanceId: null
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
            'account_manager_login' => $dto->accountManagerLogin,
            'visible' => $dto->visible,
            'source_id' => $dto->sourceId,
            'status_id' => $dto->statusId,
            'InvBalance_id' => $dto->InvBalanceId,
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
            uid: (string) $data['uid'],
            createdAt: (string) $data['created_at'],
            contact: (string) ($data['contact'] ?? ''),
            phone: (string) ($data['phone'] ?? ''),
            email: (string) ($data['email'] ?? ''),
            fullName: (string) ($data['full_name'] ?? ''),
            accountManagerId: isset($data['account_manager_id']) ? (int) $data['account_manager_id'] : null,
            accountManagerLogin: $data['account_manager_login'] ?? null,
            visible: (bool) ($data['visible'] ?? true),
            sourceId: isset($data['source_id']) ? (int) $data['source_id'] : null,
            statusId: isset($data['status_id']) ? (int) $data['status_id'] : null,
            InvBalanceId: isset($data['InvBalance_id']) ? (int) $data['InvBalance_id'] : null
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

        if ($dto->accountManagerLogin !== null) {
            $fields['account_manager_login'] = $dto->accountManagerLogin;
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
