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
     * @return User[]
     */
    public function process(Outage $outage, array $usersToBeChecked): array
    {
        $usersToBeNotified = [];

        foreach ($usersToBeChecked as $user) {
            if ($outage->matchesUser($user) && !$outage->isIdenticalPeriodAndComment($user)) {
                $user->notification = new Notification(
                    $user->id,
                    $outage->start,
                    $outage->end,
                    $outage->city,
                    $outage->streetName,
                    $user->building,
                    $outage->comment
                );
                $usersToBeNotified[] = $user;
            }
        }
        return $usersToBeNotified;
    }
}
