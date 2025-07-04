<?php

namespace crm\src\components\CommentManagement\_common\mappers;

use crm\src\components\CommentManagement\_entities\Comment;

class CommentMapper
{
    /**
     * Преобразует массив данных из БД в объект Comment.
     *
     * @param  array<string, mixed> $data
     * @return Comment|null
     */
    public static function fromArray(array $data): ?Comment
    {
        if (!isset($data['lead_id'], $data['comment'])) {
            return null;
        }

        return new Comment(
            comment: (string)$data['comment'],
            leadId: (int)$data['lead_id'],
            userId: isset($data['user_id']) ? (int)$data['user_id'] : null,
            id: isset($data['id']) ? (int)$data['id'] : null,
            depositId: isset($data['deposit_id']) ? (int)$data['deposit_id'] : null,
            createdAt: isset($data['created_at']) ? new \DateTime($data['created_at']) : null
        );
    }

    /**
     * Преобразует объект Comment в массив для БД.
     *
     * @param  Comment $comment
     * @return array<string, mixed>
     */
    public static function toArray(Comment $comment): array
    {
        return [
            'id' => $comment->id,
            'lead_id' => $comment->leadId,
            'user_id' => $comment->userId,
            'deposit_id' => $comment->depositId,
            'comment' => $comment->comment,
            'created_at' => $comment->createdAt?->format('Y-m-d H:i:s'),
        ];
    }
}
