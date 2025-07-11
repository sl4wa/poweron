<?php
namespace App\Application\Service;

use App\Application\Interface\OutageProviderInterface;
use App\Domain\Event\OutageFound;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class NotifierService
{
    public function __construct(
        private readonly OutageProviderInterface $outageProvider,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    public function notify(): int
    {
        $outages = $this->outageProvider->fetchOutages();

        foreach ($outages as $outage) {
            $event = new OutageFound($outage);
            $this->eventDispatcher->dispatch($event);
        }

        return count($outages);
    }
}
