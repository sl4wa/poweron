<?php

namespace App\Application\Interface\Service;

use App\Domain\Entity\User;
use App\Domain\ValueObject\Notification;
use App\Application\Exception\NotificationSendException;

interface NotificationSenderInterface
{
    /**
     * @throws NotificationSendException
     */
    public function send(User $user): void;
}
