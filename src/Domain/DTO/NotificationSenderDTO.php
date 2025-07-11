<?php

namespace App\Domain\DTO;

use App\Domain\Entity\Outage;
use App\Domain\Entity\User;

class NotificationSenderDTO
{
    public function __construct(
        public readonly User $user,
        public readonly Outage $outage
    ) {}
}
