<?php
namespace App\Application\Service;

use App\Application\Interface\Provider\OutageProviderInterface;
use App\Application\Interface\Repository\UserRepositoryInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use App\Domain\Event\OutageFound;

class OutageNotificationService
{
    public function __construct(
        private readonly OutageProviderInterface $outageProvider,
        private readonly UserRepositoryInterface $userRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    public function notify(): int
    {
        $outages = $this->outageProvider->fetchOutages();
        $usersToBeChecked = $this->userRepository->findAll();

        foreach ($outages as $outage) {
            $event = new OutageFound($outage, $usersToBeChecked);
            $this->eventDispatcher->dispatch($event);
        }

        return count($outages);
    }
}
