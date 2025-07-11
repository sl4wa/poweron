<?php

namespace App\Application\EventHandler;

use App\Application\Interface\UserRepositoryInterface;
use App\Domain\Event\OutageFound;
use App\Domain\Event\OutageProcessed;
use App\Domain\Service\OutageProcessor;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class OutageFoundHandler
{
    private static $usersToBeChecked = [];

    public function __construct(
        private readonly OutageProcessor $outageProcessor,
        private readonly UserRepositoryInterface $userRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    #[AsEventListener(event: OutageFound::class)]
    public function __invoke(OutageFound $event): void
    {
        $outage = $event->outage;
        if (empty(self::$usersToBeChecked)) {
            self::$usersToBeChecked = $this->userRepository->findAll();
        }

        $usersToBeNotified = $this->outageProcessor->process($outage, self::$usersToBeChecked);

        if ($usersToBeNotified) {
            $notificationEvent = new OutageProcessed($outage, $usersToBeNotified);
            $this->eventDispatcher->dispatch($notificationEvent);
        }
    }
}
