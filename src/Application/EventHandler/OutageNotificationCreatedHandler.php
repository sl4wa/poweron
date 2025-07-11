<?php
namespace App\Application\EventHandler;

use App\Application\Event\OutageNotificationCreated;
use App\Application\Exception\NotificationSendException;
use App\Application\Interface\Repository\UserRepositoryInterface;
use App\Application\Interface\Service\NotificationSenderInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final class OutageNotificationCreatedHandler
{
    public function __construct(
        private readonly NotificationSenderInterface $notificationSender,
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    #[AsEventListener(event: OutageNotificationCreated::class)]
    public function __invoke(OutageNotificationCreated $event): void
    {
        $notification = $event->notification;
        $user = $this->userRepository->find($event->notification->userId);

        try {
            $this->notificationSender->send($notification);
            $updatedUser = $user->withUpdatedOutageFromNotification($notification);
            $this->userRepository->save($updatedUser);
        } catch (NotificationSendException $e) {
            if ($e->isBlocked()) {
                $this->userRepository->remove($e->userId);
            }
        }
    }
}
