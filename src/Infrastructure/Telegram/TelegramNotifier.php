<?php
namespace App\Infrastructure\Telegram;

use App\Domain\Interface\Repository\UserRepositoryInterface;
use App\Domain\Interface\Telegram\NotificationSenderInterface;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

class TelegramNotifier implements NotificationSenderInterface
{
    public function __construct(
        private readonly Nutgram $bot,
        private readonly NotificationFormatter $formatter,
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function send(array $notifications): int
    {
        $sent = 0;

        foreach ($notifications as $notification) {
            try {
                $this->bot->sendMessage(
                    text: $this->formatter->format($notification),
                    chat_id: $notification->userId,
                    parse_mode: 'HTML'
                );

                $user = $this->userRepository->find($notification->userId);
                if ($user) {
                    $updatedUser = $user->withUpdatedOutageFromNotification($notification);
                    $this->userRepository->save($updatedUser);
                }

                $sent++;

            } catch (TelegramException $e) {
                $code    = $e->getCode();
                $message = $e->getMessage();

                // If "forbidden" error, the user has blocked the bot → remove them
                if ($code === 403 || str_contains(strtolower($message), 'forbidden')) {
                    $this->userRepository->remove($notification->userId);
                }
                // Other Telegram API errors: ignore and continue

            } catch (\Throwable) {
                // Non-Telegram errors (network, 3rd-party) — ignore and continue
            }
        }

        return $sent;
    }
}
