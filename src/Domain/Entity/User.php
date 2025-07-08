<?php
namespace App\Domain\Entity;

use App\Application\DTO\NotificationDTO;

class User
{
    public function __construct(
        public readonly int $id,
        public readonly int $streetId,
        public readonly string $streetName,
        public readonly string $building,
        public readonly string $startDate,
        public readonly string $endDate,
        public readonly string $comment
    ) {}

    public function withUpdatedOutageFromNotification(NotificationDTO $notification): self
    {
        return new self(
            $this->id,
            $this->streetId,
            $this->streetName,
            $this->building,
            $notification->start->format('c'),
            $notification->end->format('c'),
            $notification->comment
        );
    }
}
