<?php

namespace App\Application\EventHandler;

use App\Domain\Event\OutageFound;
use App\Domain\Service\OutageProcessor;
use App\Domain\Event\OutageNotificationCreated;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final class OutageFoundHandler
{
    public function __construct(
        private readonly OutageProcessor $outageProcessor,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    #[AsEventListener(event: OutageFound::class)]
    public function __invoke(OutageFound $event): void
    {
        $outage = $event->outage;
        $usersToBeChecked = $event->usersToBeChecked;

        $notification = $this->outageProcessor->process($outage, $usersToBeChecked);

        if ($notification) {
            $notificationEvent = new OutageNotificationCreated($notification, $notification->user);
            $this->eventDispatcher->dispatch($notificationEvent);
        }
    }
}
