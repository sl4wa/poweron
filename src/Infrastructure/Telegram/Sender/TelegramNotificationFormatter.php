<?php
namespace App\Infrastructure\Telegram\Sender;

use App\Domain\ValueObject\Notification;

class TelegramNotificationFormatter
{
    public function format(Notification $notification): string
    {
        return "Поточні відключення:\n"
            ."Місто: {$notification->city}\n"
            ."Вулиця: {$notification->streetName}\n"
            ."<b>{$notification->start->format('Y-m-d H:i')} – {$notification->end->format('Y-m-d H:i')}</b>\n"
            ."Коментар: {$notification->comment}\n"
            ."Будинки: {$notification->building}";
    }
}
