<?php

declare(strict_types=1);

namespace App\Domain\Interface\Provider;

use App\Domain\Entity\Outage;

interface OutageProviderInterface
{
    /**
     * @return Outage[]
     */
    public function fetchOutages(): array;
}
