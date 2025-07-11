<?php
namespace App\Application\Service;

use App\Application\Interface\Provider\OutageProviderInterface;
use App\Application\Interface\Repository\UserRepositoryInterface;
use App\Domain\Service\OutageProcessor;
use App\Domain\Event\OutageNotificationCreated;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class OutageNotificationService
{
    public function __construct(
        private readonly OutageProviderInterface $outageProvider,
        private readonly UserRepositoryInterface $userRepository,
        private readonly OutageProcessor $outageProcessor,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    public function notify(): int
    {
        $outages = $this->outageProvider->fetchOutages();
        $users = $this->userRepository->findAll();

        $notifications = $this->outageProcessor->process($outages, $users);

        $usersById = [];
        foreach ($users as $user) {
            $usersById[$user->id] = $user;
        }

        $sent = 0;
        foreach ($notifications as $notification) {
            if (isset($usersById[$notification->userId])) {
                $event = new OutageNotificationCreated($notification, $usersById[$notification->userId]);
                $this->eventDispatcher->dispatch($event);
                $sent++;
            }
        }

        return $sent;
    }
}
