<?php

namespace crm\src\Investments\Comment\_common\interfaces;

use crm\src\_common\interfaces\IResult;
use crm\src\Investments\Comment\_entities\InvComment;
use crm\src\Investments\Comment\_common\InvCommentCollection;

/**
 * Результат операций с инвестиционным комментарием или их коллекцией.
 */
interface ICommentResult extends IResult
{
    /**
     * @return InvComment|null
     */
    public function getComment(): ?InvComment;

    /**
     * @return InvCommentCollection|null
     */
    public function getCollection(): ?InvCommentCollection;

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return string|null
     */
    public function getLeadUid(): ?string;

    /**
     * @return string|null
     */
    public function getBody(): ?string;

    /**
     * @return string|null
     */
    public function getWho(): ?string;

    /**
     * @return string|null
     */
    public function getWhoId(): ?string;

    /**
     * @return int|null
     */
    public function getOption(): ?int;
}
