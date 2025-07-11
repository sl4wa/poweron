<?php
namespace App\Domain\Event;

use App\Domain\Entity\User;
use App\Domain\ValueObject\Notification;

final class OutageProcessed
{
    /**
     * @param User[] $usersToBeNotified
     */
    public function __construct(
        public readonly array $usersToBeNotified
    ) {}
}
