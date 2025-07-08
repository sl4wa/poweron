<?php
namespace App\Application\DTO;

class NotificationDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly \DateTimeImmutable $start,
        public readonly \DateTimeImmutable $end,
        public readonly string $city,
        public readonly string $street,
        public readonly string $building,
        public readonly string $comment
    ) {}
}
