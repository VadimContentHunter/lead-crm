<?php

namespace crm\src\Investments\InvComment\_mappers;

use DateTimeImmutable;
use crm\src\Investments\InvComment\_entities\InvComment;
use crm\src\Investments\InvComment\_common\DTOs\DbInvCommentDto;
use crm\src\Investments\InvComment\_common\DTOs\InvCommentInputDto;

class InvCommentMapper
{
    /**
     * Преобразует DTO из БД в сущность.
     *
     * @param  DbInvCommentDto $dto
     * @return InvComment
     */
    public static function fromDbToEntity(DbInvCommentDto $dto): InvComment
    {
        return new InvComment(
            id: $dto->id,
            leadUid: $dto->lead_uid,
            body: $dto->body,
            time: new DateTimeImmutable($dto->time),
            who: $dto->who ?? '',
            whoId: $dto->who_id,
            option: $dto->option ?? 0,
        );
    }

    /**
     * Преобразует сущность в DTO для БД.
     *
     * @param  InvComment $entity
     * @return DbInvCommentDto
     */
    public static function fromEntityToDb(InvComment $entity): DbInvCommentDto
    {
        return new DbInvCommentDto(
            id: $entity->id,
            lead_uid: $entity->leadUid,
            body: $entity->body,
            time: $entity->time?->format('Y-m-d H:i:s') ?? (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            who: $entity->who,
            who_id: $entity->whoId,
            option: $entity->option,
        );
    }

    /**
     * Преобразует входной DTO в сущность.
     *
     * @param  InvCommentInputDto $dto
     * @return InvComment
     */
    public static function fromInputToEntity(InvCommentInputDto $dto): InvComment
    {
        return new InvComment(
            id: $dto->id ?? null,
            leadUid: $dto->leadUid ?? '',
            body: $dto->body ?? '',
            time: new DateTimeImmutable(),
            who: $dto->who ?? '',
            whoId: $dto->whoId,
            option: $dto->option ?? 0,
        );
    }

    /**
     * Преобразует входной DTO напрямую в DTO для БД.
     *
     * @param  InvCommentInputDto $dto
     * @return DbInvCommentDto
     */
    public static function fromInputToDb(InvCommentInputDto $dto): DbInvCommentDto
    {
        return new DbInvCommentDto(
            id: $dto->id ?? null,
            lead_uid: $dto->leadUid ?? '',
            body: $dto->body ?? '',
            time: (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            who: $dto->who ?? '',
            who_id: $dto->whoId,
            option: $dto->option ?? 0,
        );
    }

    /**
     * Преобразует DTO для БД в ассоциативный массив.
     *
     * @param  DbInvCommentDto $dto
     * @return array<string, mixed>
     */
    public static function fromDbToArray(DbInvCommentDto $dto): array
    {
        return [
            'id' => $dto->id,
            'lead_uid' => $dto->lead_uid,
            'body' => $dto->body,
            'time' => $dto->time,
            'who' => $dto->who,
            'who_id' => $dto->who_id,
            'option' => $dto->option,
        ];
    }

    /**
     * Преобразует массив данных из БД в DTO.
     *
     * @param  array<string, mixed> $data
     * @return DbInvCommentDto
     */
    public static function fromArrayToDb(array $data): DbInvCommentDto
    {
        return new DbInvCommentDto(
            id: isset($data['id']) ? (int) $data['id'] : null,
            lead_uid: $data['lead_uid'] ?? '',
            body: $data['body'] ?? '',
            time: $data['time'] ?? (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            who: $data['who'] ?? '',
            who_id: $data['who_id'] ?? null,
            option: $data['option'] ?? 0,
        );
    }

    /**
     * Извлекает только заполненные поля из входного DTO.
     * Используется, например, при частичном обновлении комментария.
     *
     * @param  InvCommentInputDto $dto
     * @return array<string, mixed>
     */
    public static function fromInputExtractFilledFields(InvCommentInputDto $dto): array
    {
        $fields = [];

        if ($dto->id !== null) {
            $fields['id'] = $dto->id;
        }

        if ($dto->leadUid !== null) {
            $fields['lead_uid'] = $dto->leadUid;
        }

        if ($dto->body !== null) {
            $fields['body'] = $dto->body;
        }

        if ($dto->who !== null) {
            $fields['who'] = $dto->who;
        }

        if ($dto->whoId !== null) {
            $fields['who_id'] = $dto->whoId;
        }

        if ($dto->option !== null) {
            $fields['option'] = $dto->option;
        }

        return $fields;
    }
}
