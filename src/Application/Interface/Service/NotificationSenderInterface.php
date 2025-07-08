<?php

namespace App\Application\Interface\Service;

use App\Application\DTO\NotificationDTO;
use App\Application\Exception\NotificationSendException;

interface NotificationSenderInterface
{
    /**
     * @throws NotificationSendException
     */
    public function send(NotificationDTO $notification): void;
}
