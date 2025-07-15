<?php

namespace crm\src\Investments\InvComment\_entities;

use DateTimeImmutable;

/**
 * Комментарий, связанный с инвестиционным лидом.
 */
class InvComment
{
    /**
     * @param int|null            $id      Уникальный идентификатор комментария (может отсутствовать при создании)
     * @param string              $leadUid UID лида, к которому относится комментарий
     * @param string              $body    Текст комментария
     * @param DateTimeImmutable|null $time    Время создания комментария (по умолчанию — текущее)
     * @param string              $who     Имя или описание автора комментария (может быть пустым)
     * @param string|null         $whoId   ID автора комментария (опционально, например, userId)
     * @param int                 $option  Опциональный статус/тип комментария (по умолчанию 0)
     */
    public function __construct(
        public string $leadUid,
        public string $body,
        public ?DateTimeImmutable $time = null,
        public string $who = '',
        public ?string $whoId = null,
        public int $option = 0,
        public ?int $id = null,
    ) {
        $this->time = $time ?? new DateTimeImmutable();
    }
}
