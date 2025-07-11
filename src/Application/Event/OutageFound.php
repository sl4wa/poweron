<?php

namespace App\Application\Event;

use App\Domain\Entity\Outage;

class OutageFound
{
    public function __construct(
        public Outage $outage,
        public array $usersToBeChecked
    ) {}
}
