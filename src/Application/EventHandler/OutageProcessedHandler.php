<?php
namespace App\Application\EventHandler;

use App\Application\Exception\NotificationSendException;
use App\Application\Interface\NotificationSenderInterface;
use App\Application\Interface\UserRepositoryInterface;
use App\Domain\DTO\NotificationSenderDTO;
use App\Domain\Event\OutageProcessed;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final class OutageProcessedHandler
{
    private static array $notifiedUserIds = [];

    public function __construct(
        private readonly NotificationSenderInterface $notificationSender,
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    #[AsEventListener(event: OutageProcessed::class)]
    public function __invoke(OutageProcessed $event): void
    {
        $outage = $event->outage;

        foreach ($event->usersToBeNotified as $user) {
            // Only send notification if user has not been notified yet in this run
            if (in_array($user->id, self::$notifiedUserIds, true)) {
                continue;
            }

            try {
                $this->notificationSender->send(
                    new NotificationSenderDTO(
                        $user,
                        $outage
                    )
                );
                $updatedUser = $user->withUpdatedOutage($outage);
                $this->userRepository->save($updatedUser);

                // Mark user as notified
                self::$notifiedUserIds[] = $user->id;

            } catch (NotificationSendException $e) {
                if ($e->isBlocked()) {
                    $this->userRepository->remove($e->userId);
                }
            }
        }
    }
}
