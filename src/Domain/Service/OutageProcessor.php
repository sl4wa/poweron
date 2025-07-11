<?php

namespace App\Domain\Service;

use App\Domain\Entity\Outage;
use App\Domain\Entity\User;
use App\Domain\ValueObject\Notification;

class OutageProcessor
{
    /**
     * @param Outage $outage
     * @param User[] $usersToBeChecked
     * @return Notification|null
     */
    public function process(Outage $outage, array $usersToBeChecked): ?Notification
    {
        foreach ($usersToBeChecked as $user) {
            if ($outage->matchesUser($user) && !$outage->isIdenticalPeriodAndComment($user)) {
                return new Notification(
                    $user->id,
                    $outage->start,
                    $outage->end,
                    $outage->city,
                    $outage->streetName,
                    $user->building,
                    $outage->comment
                );
            }
        }
        return null;
    }
}
