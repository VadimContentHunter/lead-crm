<?php

namespace crm\src\Investments\Comment\_mappers;

use DateTimeImmutable;
use crm\src\Investments\Comment\_entities\InvComment;
use crm\src\Investments\Comment\_common\DTOs\DbInvCommentDto;
use crm\src\Investments\Comment\_common\DTOs\InvCommentInputDto;

class CommentMapper
{
    public static function fromDbToEntity(DbInvCommentDto $dto): InvComment
    {
        return new InvComment(
            id: $dto->id,
            leadUid: $dto->lead_uid,
            body: $dto->body,
            time: new DateTimeImmutable($dto->time),
            who: $dto->who,
            whoId: $dto->who_id,
            option: $dto->option ?? 0,
        );
    }

    public static function fromEntityToDb(InvComment $entity): DbInvCommentDto
    {
        return new DbInvCommentDto(
            id: $entity->id,
            lead_uid: $entity->leadUid,
            body: $entity->body,
            time: $entity->time->format('Y-m-d H:i:s'),
            who: $entity->who,
            who_id: $entity->whoId,
            option: $entity->option,
        );
    }

    public static function fromInputToEntity(InvCommentInputDto $dto): InvComment
    {
        return new InvComment(
            id: uniqid('com_', true),
            leadUid: $dto->leadUid,
            body: $dto->body,
            time: new DateTimeImmutable(),
            who: $dto->who ?? '',
            whoId: $dto->whoId,
            option: $dto->option ?? 0,
        );
    }

    public static function fromInputToDb(InvCommentInputDto $dto): DbInvCommentDto
    {
        return new DbInvCommentDto(
            id: uniqid('com_', true),
            lead_uid: $dto->leadUid,
            body: $dto->body,
            time: (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            who: $dto->who ?? '',
            who_id: $dto->whoId,
            option: $dto->option ?? 0,
        );
    }

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

    public static function fromArrayToDb(array $data): DbInvCommentDto
    {
        return new DbInvCommentDto(
            id: $data['id'] ?? uniqid('com_', true),
            lead_uid: $data['lead_uid'],
            body: $data['body'],
            time: $data['time'],
            who: $data['who'] ?? '',
            who_id: $data['who_id'] ?? null,
            option: $data['option'] ?? 0,
        );
    }

    public static function fromInputExtractFilledFields(InvCommentInputDto $dto): array
    {
        $fields = [];

        $fields['lead_uid'] = $dto->leadUid;
        $fields['body'] = $dto->body;

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
