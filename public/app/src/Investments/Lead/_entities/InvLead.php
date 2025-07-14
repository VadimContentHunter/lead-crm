<?php

namespace crm\src\Investments\Lead\_entities;

use crm\src\Investments\Balance\_entities\InvBalance;
use crm\src\Investments\Comment\_common\InvCommentCollection;
use crm\src\Investments\Deposit\_common\InvDepositCollection;
use crm\src\Investments\Activity\_common\InvActivityCollection;

class InvLead
{
    public function __construct(
        public string $uid,
        public string $source,
        public string $contact,
        public string $phone,
        public string $email,
        public string $fullName,
        public int $createdAt,
        public string $accountManager,
        public string $status,
        public bool $visible = true,
        public InvBalance $balance = new InvBalance(),
        public InvDepositCollection $deposits = new InvDepositCollection(),
        public InvActivityCollection $activities = new InvActivityCollection(),
        public InvCommentCollection $comments = new InvCommentCollection(),
    ) {
    }
}
