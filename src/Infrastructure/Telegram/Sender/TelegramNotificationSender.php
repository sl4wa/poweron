<?php

namespace App\Infrastructure\Telegram\Sender;

use App\Application\DTO\NotificationDTO;
use App\Application\Exception\NotificationSendException;
use App\Application\Interface\Service\NotificationSenderInterface;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

class TelegramNotificationSender implements NotificationSenderInterface
{
    public function __construct(
        private readonly Nutgram $bot,
        private readonly NotificationFormatter $formatter,
    ) {}

    public function send(NotificationDTO $notification): void
    {
        try {
            $this->bot->sendMessage(
                text: $this->formatter->format($notification),
                chat_id: $notification->userId,
                parse_mode: 'HTML'
            );
        } catch (TelegramException $e) {
            throw new NotificationSendException(
                $notification->userId,
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        } catch (\Throwable $e) {
            throw new NotificationSendException(
                $notification->userId,
                $e->getMessage(),
                0,
                $e
            );
        }
    }
}
