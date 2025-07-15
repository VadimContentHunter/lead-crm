<?php

namespace crm\src\Investments\InvComment\_common;

use crm\src\Investments\InvComment\_entities\InvComment;

/**
 * Коллекция комментариев к инвестиционному лиду.
 */
final class InvCommentCollection
{
    /**
     * @var InvComment[] Список комментариев
     */
    private array $items = [];

    /**
     * @param InvComment[] $items Изначальный список комментариев
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * Добавляет комментарий в коллекцию.
     *
     * @param  InvComment $InvComment Комментарий для добавления
     * @return void
     */
    public function add(InvComment $InvComment): void
    {
        $this->items[] = $InvComment;
    }

    /**
     * Возвращает последние комментарии, начиная с конца списка.
     *
     * @param  int $limit Максимальное количество комментариев (по умолчанию 5)
     * @return InvComment[]
     */
    public function getLatest(int $limit = 5): array
    {
        return array_slice(array_reverse($this->items), 0, $limit);
    }

    /**
     * Возвращает все комментарии.
     *
     * @return InvComment[]
     */
    public function getAll(): array
    {
        return $this->items;
    }
}
