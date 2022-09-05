<?php

namespace App\Enums;

enum UserStatus: string
{
    case Pending = 'pending-approval';
    case Approved = 'approved';
    case Blocked = 'blocked';
}
