<?php

namespace crm\src\Investments\Comment\_common;

use crm\src\Investments\Comment\_entities\InvComment;

final class InvCommentCollection
{
    /**
     * @param InvComment[] $items
     */
    public function __construct(
        private array $items = []
    ) {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    public function add(InvComment $comment): void
    {
        $this->items[] = $comment;
    }

    /**
     * @return InvComment[]
     */
    public function latest(int $limit = 5): array
    {
        return array_slice(array_reverse($this->items), 0, $limit);
    }

    /**
     * @return InvComment[]
     */
    public function all(): array
    {
        return $this->items;
    }
}
