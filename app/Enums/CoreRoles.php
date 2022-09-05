<?php

namespace App\Enums;

enum CoreRoles: string
{
    case SuperAdministrator = 'Super Administrator';
    case Contributor = 'Contributor';
    case Manager = 'Manager';
    case Subscriber = 'Subscriber';
}