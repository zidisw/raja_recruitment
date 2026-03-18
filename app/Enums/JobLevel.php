<?php

declare(strict_types=1);

namespace App\Enums;

enum JobLevel: string
{
    case Staff = 'staff';
    case NonStaff = 'non_staff';
}