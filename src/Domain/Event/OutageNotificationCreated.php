<?php
namespace App\Domain\Event;

use App\Domain\ValueObject\Notification;
use App\Domain\Entity\User;

final class OutageNotificationCreated
{
    public function __construct(
        public readonly Notification $notification,
        public readonly User $user,
    ) {}
}
