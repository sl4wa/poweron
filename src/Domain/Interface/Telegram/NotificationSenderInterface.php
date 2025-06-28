<?php

declare(strict_types=1);

namespace App\Domain\Interface\Telegram;

use App\Domain\ValueObject\Notification;

interface NotificationSenderInterface
{
    /**
     * @param Notification[] $notifications
     */
    public function send(array $notifications): int;
}
