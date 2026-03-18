<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case User = 'user';
    case Admin = 'admin';
    case SuperAdmin = 'super_admin';

    // Legacy roles kept for backward compatibility with existing data.
    case HR = 'hr';
    case Interviewer = 'interviewer';
    case Candidate = 'candidate';

    public function label(): string
    {
        return match ($this) {
            self::User, self::Candidate => 'User',
            self::Admin, self::HR, self::Interviewer => 'Admin',
            self::SuperAdmin => 'Super Admin',
        };
    }

    public function isUser(): bool
    {
        return in_array($this, [self::User, self::Candidate], true);
    }

    public function isAdmin(): bool
    {
        return in_array($this, [self::Admin, self::HR, self::Interviewer], true);
    }

    public function isSuperAdmin(): bool
    {
        return $this === self::SuperAdmin;
    }

    public function canAccessRecruitment(): bool
    {
        return $this->isAdmin() || $this->isSuperAdmin();
    }

    public function normalized(): self
    {
        return match ($this) {
            self::Candidate => self::User,
            self::HR, self::Interviewer => self::Admin,
            default => $this,
        };
    }

    public static function assignableCases(): array
    {
        return [self::User, self::Admin, self::SuperAdmin];
    }
}