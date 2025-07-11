<?php
namespace App\Infrastructure\Telegram\Sender;

use App\Domain\DTO\NotificationSenderDTO;

class TelegramNotificationFormatter
{
    public function format(NotificationSenderDTO $dto): string
    {
        return "Поточні відключення:\n"
            ."Місто: {$dto->outage->city}\n"
            ."Вулиця: {$dto->outage->streetName}\n"
            ."<b>{$dto->outage->start->format('Y-m-d H:i')} – {$dto->outage->end->format('Y-m-d H:i')}</b>\n"
            ."Коментар: {$dto->outage->comment}\n"
            ."Будинки: {$dto->user->building}";
    }
}
