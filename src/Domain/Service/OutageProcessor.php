<?php
namespace App\Domain\Service;

use App\Domain\Entity\Outage;
use App\Domain\Interface\Repository\UserRepositoryInterface;
use App\Domain\ValueObject\Notification;

class OutageProcessor
{
    public function __construct(private UserRepositoryInterface $userRepository) {}

    /**
     * @param Outage[] $outages
     * @return Notification[]
     */
    public function process(array $outages): array
    {
        $users = $this->userRepository->findAll();
        $sent = [];
        $notifications = [];

        foreach ($outages as $outage) {
            foreach ($users as $user) {
                if (isset($sent[$user->id]) && $sent[$user->id]) {
                    continue;
                }
                if ($outage->matchesUser($user)) {
                    if ($outage->isIdenticalPeriodAndComment($user)) {
                        $sent[$user->id] = true;
                        continue;
                    }
                    $notifications[] = new Notification(
                        $user->id,
                        $outage->start,
                        $outage->end,
                        $outage->city,
                        $outage->streetName,
                        $user->building,
                        $outage->comment
                    );

                    $sent[$user->id] = true;
                }
            }
        }

        return $notifications;
    }
}
