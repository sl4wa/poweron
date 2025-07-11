<?php

namespace App\Application\Interface;

use App\Application\Exception\NotificationSendException;
use App\Domain\DTO\NotificationSenderDTO;

interface NotificationSenderInterface
{
    /**
     * @throws NotificationSendException
     */
    public function send(NotificationSenderDTO $dto): void;
}
