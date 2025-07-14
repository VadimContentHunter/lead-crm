<?php

namespace crm\src\Investments\Activity\_entities;

enum DealDirection: string
{
    case LONG = 'long';
    case SHORT = 'short';
}
