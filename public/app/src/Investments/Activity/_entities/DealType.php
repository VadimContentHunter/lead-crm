<?php

namespace crm\src\Investments\Activity\_entities;

enum DealType: string
{
    case ACTIVE = 'active';
    case CLOSED = 'closed';
}
