<?php

namespace App\Application\Service;

use App\Application\Exception\NotificationSendException;
use App\Application\Interface\Service\NotificationSenderInterface;
use App\Domain\Interface\Repository\UserRepositoryInterface;
use App\Domain\ValueObject\Notification;


class NotificationProcessor
{
    public function __construct(
        private readonly NotificationSenderInterface $notificationSender,
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    /**
     * @param Notification[] $notifications
     * @return int Number of successfully sent notifications
     */
    public function process(array $notifications): int
    {
        $sent = 0;

        foreach ($notifications as $notification) {
            try {
                $this->notificationSender->send($notification);

                $user = $this->userRepository->find($notification->userId);
                if ($user) {
                    $updatedUser = $user->withUpdatedOutageFromNotification($notification);
                    $this->userRepository->save($updatedUser);
                }

                $sent++;
            } catch (NotificationSendException $e) {
                if ($e->isBlocked()) {
                    $this->userRepository->remove($e->userId);
                }
            }
        }

        return $sent;
    }
}
