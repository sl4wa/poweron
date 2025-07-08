<?php
namespace App\Domain\Service;

use App\Application\DTO\NotificationDTO;
use App\Domain\Entity\Outage;
use App\Domain\Interface\Repository\UserRepositoryInterface;

class OutageProcessor
{
    public function __construct(private UserRepositoryInterface $userRepository) {}

    /**
     * @param Outage[] $outages
     * @return NotificationDTO[]
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
                    $notifications[] = new NotificationDTO(
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
