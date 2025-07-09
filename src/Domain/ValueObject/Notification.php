<?php

namespace App\Domain\ValueObject;

class Notification
{
    public function __construct(
        public int $userId,
        public \DateTimeImmutable $start,
        public \DateTimeImmutable $end,
        public string $city,
        public string $streetName,
        public string $building,
        public ?string $comment
    ) {}
}
