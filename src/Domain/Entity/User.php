<?php
namespace App\Domain\Entity;

use App\Domain\ValueObject\Notification;

class User
{
    public function __construct(
        public readonly int $id,
        public readonly int $streetId,
        public readonly string $streetName,
        public readonly string $building,
        public readonly string $startDate,
        public readonly string $endDate,
        public readonly string $comment,
        public ?Notification $notification = null
    ) {}

    public function withUpdatedOutageFromNotification(): self
    {
        return new self(
            $this->id,
            $this->streetId,
            $this->streetName,
            $this->building,
            $this->notification->start->format('c'),
            $this->notification->end->format('c'),
            $this->notification->comment
        );
    }
}
