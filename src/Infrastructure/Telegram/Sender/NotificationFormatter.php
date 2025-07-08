<?php
namespace App\Infrastructure\Telegram\Sender;

use App\Application\DTO\NotificationDTO;

class NotificationFormatter
{
    public function format(NotificationDTO $notification): string
    {
        return "Поточні відключення:\n"
            ."Місто: {$notification->city}\n"
            ."Вулиця: {$notification->street}\n"
            ."<b>{$notification->start->format('Y-m-d H:i')} – {$notification->end->format('Y-m-d H:i')}</b>\n"
            ."Коментар: {$notification->comment}\n"
            ."Будинки: {$notification->building}";
    }
}
