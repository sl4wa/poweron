<?php
namespace App\Application\EventHandler;

use App\Domain\Event\OutageNotificationCreated;
use App\Application\Interface\Service\NotificationSenderInterface;
use App\Application\Interface\Repository\UserRepositoryInterface;
use App\Application\Exception\NotificationSendException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final class SendOutageNotificationHandler
{
    public function __construct(
        private readonly NotificationSenderInterface $notificationSender,
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    #[AsEventListener(event: OutageNotificationCreated::class)]
    public function __invoke(OutageNotificationCreated $event): void
    {
        $notification = $event->notification;
        $user = $event->user;

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
