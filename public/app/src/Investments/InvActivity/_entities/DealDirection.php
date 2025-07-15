<?php

namespace crm\src\Investments\InvActivity\_entities;

enum DealDirection: string
{
    case LONG = 'long';
    case SHORT = 'short';
}
