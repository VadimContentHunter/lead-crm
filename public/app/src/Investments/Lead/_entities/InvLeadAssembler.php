<?php

namespace crm\src\Investments\Lead\_entities;

// use crm\src\Investments\Balance\_repositories\InvBalanceRepository;
// use crm\src\Investments\Deposit\_repositories\InvDepositRepository;
// use crm\src\Investments\InvActivity\_repositories\InvInvActivityRepository;
// use crm\src\Investments\Comment\_repositories\InvCommentRepository;

// class InvLeadAssembler
// {
//     public function __construct(
//         private InvBalanceRepository $balanceRepo,
//         private InvDepositRepository $depositRepo,
//         private InvInvActivityRepository $InvActivityRepo,
//         private InvCommentRepository $commentRepo
//     ) {
//     }

//     public function assemble(array $data): InvLead
//     {
//         return new InvLead(
//             uid: $data['uid'],
//             source: $data['source'],
//             contact: $data['contact'],
//             phone: $data['phone'],
//             email: $data['email'],
//             fullName: $data['fullName'],
//             createdAt: $data['createdAt'],
//             accountManager: $data['accountManager'],
//             status: $data['status'],
//             visible: $data['visible'] ?? true,
//             balance: $this->balanceRepo->getByLeadUid($data['uid']),
//             deposits: $this->depositRepo->getByLeadUid($data['uid']),
//             activities: $this->InvActivityRepo->getByLeadUid($data['uid']),
//             comments: $this->commentRepo->getByLeadUid($data['uid']),
//         );
//     }
// }
