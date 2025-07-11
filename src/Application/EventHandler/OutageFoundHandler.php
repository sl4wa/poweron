<?php

namespace App\Application\EventHandler;

use App\Application\Event\OutageFound;
use App\Application\Event\OutageNotificationCreated;
use App\Domain\Service\OutageProcessor;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

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
            $notificationEvent = new OutageNotificationCreated($notification);
            $this->eventDispatcher->dispatch($notificationEvent);
        }
    }
}
