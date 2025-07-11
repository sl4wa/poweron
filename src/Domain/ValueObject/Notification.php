<?php

namespace App\Domain\ValueObject;

use App\Domain\Entity\User;

class Notification
{
    public function __construct(
        public User $user,
        public \DateTimeImmutable $start,
        public \DateTimeImmutable $end,
        public string $city,
        public string $streetName,
        public string $building,
        public ?string $comment
    ) {}
}
