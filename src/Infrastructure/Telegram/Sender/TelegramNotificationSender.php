<?php

namespace App\Infrastructure\Telegram\Sender;

use App\Application\Exception\NotificationSendException;
use App\Application\Interface\NotificationSenderInterface;
use App\Domain\DTO\NotificationSenderDTO;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

class TelegramNotificationSender implements NotificationSenderInterface
{
    public function __construct(
        private readonly Nutgram $bot,
        private readonly TelegramNotificationFormatter $formatter,
    ) {}

    public function send(NotificationSenderDTO $dto): void
    {
        try {
            $this->bot->sendMessage(
                text: $this->formatter->format($dto),
                chat_id: $dto->user->id,
                parse_mode: 'HTML'
            );
        } catch (TelegramException $e) {
            throw new NotificationSendException(
                $dto->user->id,
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        } catch (\Throwable $e) {
            throw new NotificationSendException(
                $dto->user->id,
                $e->getMessage(),
                0,
                $e
            );
        }
    }
}
