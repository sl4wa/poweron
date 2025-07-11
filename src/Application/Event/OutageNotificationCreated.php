<?php
namespace App\Application\Event;

use App\Domain\Entity\User;
use App\Domain\ValueObject\Notification;

final class OutageNotificationCreated
{
    public function __construct(
        public readonly Notification $notification
    ) {}
}
