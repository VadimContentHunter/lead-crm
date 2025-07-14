<?php

namespace crm\src\Investments\Comment\_common;

use crm\src\Investments\Comment\_entities\InvComment;

final class InvCommentCollection
{
    /**
     * @var InvComment[]
     */
    private array $items = [];

    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    public function add(InvComment $comment): void
    {
        $this->items[] = $comment;
    }

    public function latest(int $limit = 5): array
    {
        return array_slice(array_reverse($this->items), 0, $limit);
    }

    public function all(): array
    {
        return $this->items;
    }
}
