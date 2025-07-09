<?php

namespace App\Application\Interface\Service;

use App\Domain\ValueObject\Notification;
use App\Application\Exception\NotificationSendException;

interface NotificationSenderInterface
{
    /**
     * @throws NotificationSendException
     */
    public function send(Notification $notification): void;
}
