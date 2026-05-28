<?php

declare(strict_types=1);

namespace App\Enums;

enum OfferingStatus: string
{
    case WAITING_RESPONSE = 'waiting_response';
    case SIGNED = 'signed';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::WAITING_RESPONSE => __('Waiting Candidate Upload'),
            self::SIGNED => __('Signed by Candidate'),
            self::ACCEPTED => __('Accepted'),
            self::REJECTED => __('Rejected'),
        };
    }
}
